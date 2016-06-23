<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Security;

use eTraxis\Dictionary\AuthenticationProvider;
use eTraxis\Entity\User;
use eTraxis\Service\Ldap\LdapInterface;
use eTraxis\SimpleBus\Users\RegisterUserCommand;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * LDAP authenticator.
 */
class LdapAuthenticator extends AbstractGuardAuthenticator
{
    protected $router;
    protected $session;
    protected $command_bus;
    protected $ldap;
    protected $basedn;

    /**
     * Dependency Injection constructor.
     *
     * @param   RouterInterface  $router
     * @param   SessionInterface $session
     * @param   MessageBus       $command_bus
     * @param   LdapInterface    $ldap
     * @param   string           $basedn
     */
    public function __construct(
        RouterInterface  $router,
        SessionInterface $session,
        MessageBus       $command_bus,
        LdapInterface    $ldap,
        string           $basedn)
    {
        $this->router      = $router;
        $this->session     = $session;
        $this->command_bus = $command_bus;
        $this->ldap        = $ldap;
        $this->basedn      = $basedn;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        if ($request->isXmlHttpRequest()) {
            $this->session->remove('_security.main.target_path');
        }
        else {
            $this->session->set('_security.main.target_path', $request->getRequestUri());
        }

        return new RedirectResponse($this->router->generate('login'));
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
        $entry = $this->ldap->find($this->basedn, $credentials['username'], ['cn', 'mail']);

        if ($entry === false) {
            return null;
        }

        $user = new User(AuthenticationProvider::LDAP);

        $user->setUsername($credentials['username']);
        $user->setFullname($entry['cn']);
        $user->setEmail($entry['mail']);

        return new CurrentUser($user);
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
        /** @var CurrentUser $user */
        $user = $token->getUser();

        // Save user info in database.
        $command = new RegisterUserCommand([
            'username' => $user->getUsername(),
            'fullname' => $user->getFullname(),
            'email'    => $user->getEmail(),
        ]);

        $this->command_bus->handle($command);

        // An URL the user was trying to reach before authentication.
        $originalUrl = $this->session->get('_security.main.target_path', $this->router->generate('homepage'));

        return new RedirectResponse($originalUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return true;
    }
}
