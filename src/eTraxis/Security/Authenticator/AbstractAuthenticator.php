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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Abstract authenticator.
 */
abstract class AbstractAuthenticator implements GuardAuthenticatorInterface
{
    protected $router;
    protected $session;

    /**
     * Dependency Injection constructor.
     *
     * @param   RouterInterface  $router
     * @param   SessionInterface $session
     */
    public function __construct(RouterInterface $router, SessionInterface $session)
    {
        $this->router  = $router;
        $this->session = $session;
    }

    /**
     * Returns URL the user was trying to reach before authentication.
     *
     * @return  string
     */
    protected function getOriginalUrl()
    {
        return $this->session->get('_security.main.target_path', $this->router->generate('homepage'));
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
    public function createAuthenticatedToken(UserInterface $user, $providerKey)
    {
        return new PostAuthenticationGuardToken(
            $user,
            $providerKey,
            $user->getRoles()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return true;
    }
}
