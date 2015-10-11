<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Security;

use eTraxis\CommandBus\CommandBusInterface;
use eTraxis\CommandBus\Users\RegisterUserCommand;
use eTraxis\CommandBus\ValidationException;
use eTraxis\Service\LdapInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * LDAP authenticator.
 */
class LdapAuthenticator implements SimpleFormAuthenticatorInterface
{
    protected $logger;
    protected $command_bus;
    protected $ldap;
    protected $basedn;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface     $logger      Debug logger.
     * @param   CommandBusInterface $command_bus Command bus.
     * @param   LdapInterface       $ldap        LDAP service.
     * @param   string              $basedn      Base DN to search in.
     */
    public function __construct(
        LoggerInterface     $logger,
        CommandBusInterface $command_bus,
        LdapInterface       $ldap,
        $basedn)
    {
        $this->logger      = $logger;
        $this->command_bus = $command_bus;
        $this->ldap        = $ldap;
        $this->basedn      = $basedn;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$this->ldap->authenticate($this->basedn, $token->getUsername(), $token->getCredentials())) {
            $this->logger->info('LDAP authentication is failed.');
            throw new AuthenticationException('Bad credentials');
        }

        $entry = $this->ldap->find($this->basedn, $token->getUsername(), ['cn', 'mail']);

        if (!$entry) {
            throw new AuthenticationException('Bad credentials');
        }

        try {
            $command = new RegisterUserCommand([
                'username' => $token->getUsername(),
                'fullname' => $entry['cn'],
                'email'    => $entry['mail'],
            ]);

            $this->command_bus->handle($command);
        }
        catch (ValidationException $exception) {
            throw new AuthenticationException('Bad credentials');
        }

        try {
            $user = $userProvider->loadUserByUsername($token->getUsername());
        }
        catch (UsernameNotFoundException $exception) {
            throw new AuthenticationException('Bad credentials');
        }

        return new UsernamePasswordToken(
            $user,
            null,
            $providerKey,
            $user->getRoles()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * {@inheritdoc}
     */
    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }
}
