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

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Provides special response in case of unauthorized (401) AJAX request.
 */
class UnauthorizedAjaxRequest
{
    protected $router;
    protected $translator;
    protected $authentication_utils;

    /**
     * Dependency Injection constructor.
     *
     * @param   Router              $router
     * @param   TranslatorInterface $translator
     * @param   AuthenticationUtils $authentication_utils
     */
    public function __construct(
        Router              $router,
        TranslatorInterface $translator,
        AuthenticationUtils $authentication_utils)
    {
        $this->router               = $router;
        $this->translator           = $translator;
        $this->authentication_utils = $authentication_utils;
    }

    /**
     * Overrides the response if user is redirected to login page and it was an AJAX request.
     *
     * @param   FilterResponseEvent $event
     */
    public function onResponse(FilterResponseEvent $event)
    {
        $request  = $event->getRequest();
        $response = $event->getResponse();

        if ($request->isXmlHttpRequest() && $response->isRedirect($this->router->generate('login', [], Router::ABSOLUTE_URL))) {

            $error   = $this->authentication_utils->getLastAuthenticationError();
            $message = $error === null ? 'security.session_expired' : $error->getMessage();

            $event->setResponse(new Response($this->translator->trans($message), Response::HTTP_UNAUTHORIZED));
        }
    }
}
