<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\EventListener;

use eTraxis\Voter\UserVoter;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Kernel events listener.
 */
class KernelListener implements EventSubscriberInterface
{
    protected $router;
    protected $translator;
    protected $authorization_checker;
    protected $token_storage;
    protected $locale;

    /**
     * Dependency Injection constructor.
     *
     * @param   Router                        $router
     * @param   TranslatorInterface           $translator
     * @param   AuthorizationCheckerInterface $authorization_checker
     * @param   TokenStorageInterface         $token_storage
     * @param   string                        $locale
     */
    public function __construct(
        Router                        $router,
        TranslatorInterface           $translator,
        AuthorizationCheckerInterface $authorization_checker,
        TokenStorageInterface         $token_storage,
        $locale)
    {
        $this->router                = $router;
        $this->translator            = $translator;
        $this->authorization_checker = $authorization_checker;
        $this->token_storage         = $token_storage;
        $this->locale                = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST    => ['onKernelRequest', 10],
            KernelEvents::RESPONSE   => 'onKernelResponse',
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    /**
     * The REQUEST event occurs at the very beginning of request dispatching.
     *
     * @param   GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // Override global locale with current user's one.
        if ($request->hasPreviousSession()) {

            // Try to see if the locale has been set as a _locale routing parameter.
            if ($locale = $request->attributes->get('_locale')) {
                $request->getSession()->set('_locale', $locale);
            }
            else {
                // If no explicit locale has been set on this request, use one from the session.
                $request->setLocale($request->getSession()->get('_locale', $this->locale));
            }
        }
    }

    /**
     * The RESPONSE event occurs once a response was created for replying to a request.
     *
     * @param   FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request  = $event->getRequest();
        $response = $event->getResponse();

        // If user is redirected to login page and it was an AJAX request - override the response.
        if ($request->isXmlHttpRequest() && $response->isRedirect($this->router->generate('login', [], Router::ABSOLUTE_URL))) {
            $event->setResponse(new Response($this->translator->trans('security.session_expired'), Response::HTTP_UNAUTHORIZED));
        }
    }

    /**
     * The CONTROLLER event occurs once a controller was found for handling a request.
     *
     * @param   FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
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
        if ($this->authorization_checker->isGranted(UserVoter::SET_EXPIRED_PASSWORD, $this->token_storage->getToken()->getUser())) {

            $url = $this->router->generate('set_expired_password');

            $event->setController(
                function () use ($url) {
                    return new RedirectResponse($url);
                }
            );
        }
    }
}
