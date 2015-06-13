<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------


namespace AppBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Kernel events listener.
 */
class KernelListener implements EventSubscriberInterface
{
    /** @var Router */
    protected $router;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var string Default locale */
    protected $locale;

    /**
     * Dependency Injection constructor.
     *
     * @param   Router              $router
     * @param   TranslatorInterface $translator
     * @param   string              $locale
     */
    public function __construct(Router $router, TranslatorInterface $translator, $locale)
    {
        $this->router     = $router;
        $this->translator = $translator;
        $this->locale     = $locale;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
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
}
