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
use eTraxis\SimpleBus\Users\RegisterUserCommand;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;
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
    protected $user;
    protected $password;

    /**
     * Dependency Injection constructor.
     *
     * @param   RouterInterface  $router
     * @param   SessionInterface $session
     * @param   MessageBus       $command_bus
     * @param   LdapInterface    $ldap
     * @param   string           $basedn
     * @param   string           $user
     * @param   string           $password
     */
    public function __construct(
        RouterInterface  $router,
        SessionInterface $session,
        MessageBus       $command_bus,
        LdapInterface    $ldap = null,
        string           $basedn = null,
        string           $user = null,
        string           $password = null)
    {
        $this->router      = $router;
        $this->session     = $session;
        $this->command_bus = $command_bus;
        $this->ldap        = $ldap;
        $this->basedn      = $basedn;
        $this->user        = $user;
        $this->password    = $password;
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
        if (!$this->ldap) {
            return null;
        }

        try {
            if ($this->user) {
                $this->ldap->bind($this->user, $this->password);
            }

            $query   = $this->ldap->query($this->basedn, "(uid={$credentials['username']})", ['filter' => ['cn', 'mail']]);
            $entries = $query->execute();

            if (count($entries) === 0) {
                return null;
            }

            $entry = $entries[0];

            $fullnames = $entry->getAttribute('cn');
            $emails    = $entry->getAttribute('mail');

            if (!$fullnames || !$emails) {
                return null;
            }

            $user = new User(AuthenticationProvider::LDAP);

            $user->setUsername($credentials['username']);
            $user->setFullname($fullnames[0]);
            $user->setEmail($emails[0]);

            return new CurrentUser($user);
        }
        catch (ConnectionException $exception) {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!$this->ldap) {
            return false;
        }

        try {
            $this->ldap->bind("uid={$credentials['username']},{$this->basedn}", $credentials['password']);
        }
        catch (ConnectionException $exception) {
            return false;
        }

        return true;
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

    /**
     * Creates LDAP client connected with specified LDAP server.
     *
     * @param   string $host       Server name w/out protocol and port.
     * @param   int    $port       Connection port (389 by default).
     * @param   string $encryption "ssl", "tls", or NULL.
     *
     * @return  LdapInterface|null
     */
    public static function connect(string $host = null, int $port = null, string $encryption = null)
    {
        if (!$host) {
            return null;
        }

        $config = [
            'host'       => $host,
            'port'       => $port ?: 389,
            'encryption' => $encryption ?: 'none',
        ];

        return Ldap::create('ext_ldap', $config);
    }
}
