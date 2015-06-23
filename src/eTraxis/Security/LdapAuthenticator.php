<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Security;

use eTraxis\Exception\ResponseException;
use eTraxis\Service\LdapService;
use eTraxis\SimpleBus\Command\User\RegisterUserCommand;
use Psr\Log\LoggerInterface;
use SimpleBus\Message\Bus\MessageBus;
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
    /** @var LoggerInterface */
    protected $logger;

    /** @var MessageBus */
    protected $command_bus;

    /** @var LdapService */
    protected $ldap;

    /** @var string */
    protected $basedn;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface $logger      Debug logger.
     * @param   MessageBus      $command_bus Command bus.
     * @param   LdapService     $ldap        LDAP service.
     * @param   string          $basedn      Base DN to search in.
     */
    public function __construct(
        LoggerInterface $logger,
        MessageBus      $command_bus,
        LdapService     $ldap,
        $basedn)
    {
        $this->logger      = $logger;
        $this->command_bus = $command_bus;
        $this->ldap        = $ldap;
        $this->basedn      = $basedn;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$this->ldap->isConnected()) {
            $this->logger->info('LDAP is not connected.');
            throw new AuthenticationException('Bad credentials');
        }

        if (!$this->ldap->authenticate($this->basedn, $token->getUsername(), $token->getCredentials())) {
            $this->logger->info('LDAP authentication is failed.');
            throw new AuthenticationException('Bad credentials');
        }

        $entry = $this->ldap->find($this->basedn, $token->getUsername(), ['cn', 'mail']);

        if (!$entry) {
            throw new AuthenticationException('Bad credentials');
        }

        try {
            $command = new RegisterUserCommand();

            $command->username = $token->getUsername();
            $command->fullname = $entry['cn'];
            $command->email    = $entry['mail'];

            $this->command_bus->handle($command);
        }
        catch (ResponseException $e) {
            $this->logger->error($e->getMessage(), [$e->getCode()]);
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
     * {@inheritDoc}
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * {@inheritDoc}
     */
    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }
}
