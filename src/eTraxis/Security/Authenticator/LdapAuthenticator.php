<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Security\Authenticator;

use eTraxis\Service\LdapInterface;
use eTraxis\SimpleBus\Users\RegisterUserCommand;
use Psr\Log\LoggerInterface;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * LDAP authenticator.
 */
class LdapAuthenticator extends AbstractAuthenticator
{
    protected $logger;
    protected $command_bus;
    protected $ldap;
    protected $basedn;

    /**
     * Dependency Injection constructor.
     *
     * @param   RouterInterface  $router
     * @param   SessionInterface $session
     * @param   LoggerInterface  $logger
     * @param   MessageBus       $command_bus
     * @param   LdapInterface    $ldap
     * @param   string           $basedn
     */
    public function __construct(
        RouterInterface  $router,
        SessionInterface $session,
        LoggerInterface  $logger,
        MessageBus       $command_bus,
        LdapInterface    $ldap,
        $basedn)
    {
        parent::__construct($router, $session);

        $this->logger      = $logger;
        $this->command_bus = $command_bus;
        $this->ldap        = $ldap;
        $this->basedn      = $basedn;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        if (!$request->request->has('_username')) {
            return null;
        }

        return [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials['username']);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->ldap->authenticate($this->basedn, $credentials['username'], $credentials['password']);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $entry = $this->ldap->find($this->basedn, $token->getUsername(), ['cn', 'mail']);

        if ($entry) {

            $command = new RegisterUserCommand([
                'username' => $token->getUsername(),
                'fullname' => $entry['cn'],
                'email'    => $entry['mail'],
            ]);

            $this->command_bus->handle($command);
        }

        return new RedirectResponse($this->getOriginalUrl());
    }
}
