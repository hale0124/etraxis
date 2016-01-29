<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\EventListener;

use eTraxis\Entity\User;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Forces user to change password if it's expired.
 */
class ExpiredPassword
{
    protected $router;
    protected $authorization_checker;
    protected $token_storage;

    /**
     * Dependency Injection constructor.
     *
     * @param   Router                        $router
     * @param   AuthorizationCheckerInterface $authorization_checker
     * @param   TokenStorageInterface         $token_storage
     */
    public function __construct(
        Router                        $router,
        AuthorizationCheckerInterface $authorization_checker,
        TokenStorageInterface         $token_storage)
    {
        $this->router                = $router;
        $this->authorization_checker = $authorization_checker;
        $this->token_storage         = $token_storage;
    }

    /**
     * Checks for password is expired before any controller's action is executed.
     *
     * @param   FilterControllerEvent $event
     */
    public function onAction(FilterControllerEvent $event)
    {
        $request     = $event->getRequest();
        $controllers = $event->getController();

        // Controller passed can be either a class or a Closure.
        // This is not usual in Symfony but it may happen.
        // If it is a class, it comes in array format.
        if (!is_array($controllers)) {
            return;
        }

        if (!is_object($controller = $controllers[0])) {
            return;
        }

        // Avoid redirection loop.
        if ($request->get('_route') == 'set_expired_password') {
            return;
        }

        // Determine controller's bundle name.
        $class  = get_class($controller);
        $bundle = substr($class, 0, strpos($class, '\\'));

        // Skip any 3rd party controllers.
        if ($bundle != 'AppBundle') {
            return;
        }

        // Skip for anonymous user.
        if (!$this->authorization_checker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return;
        }

        // Redirect to "Set expired password" page.
        if ($this->authorization_checker->isGranted(User::SET_EXPIRED_PASSWORD, $this->token_storage->getToken()->getUser())) {

            $url = $this->router->generate('set_expired_password');

            $event->setController(
                function () use ($url) {
                    return new RedirectResponse($url);
                }
            );
        }
    }
}
