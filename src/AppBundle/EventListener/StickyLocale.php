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

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * "Sticky" user's locale.
 */
class StickyLocale
{
    protected $session;
    protected $locale;

    /**
     * Dependency Injection constructor.
     *
     * @param   SessionInterface $session
     * @param   string           $locale
     */
    public function __construct(SessionInterface $session, string $locale)
    {
        $this->session = $session;
        $this->locale  = $locale;
    }

    /**
     * Save user's locale when one has been authenticated.
     *
     * @param   InteractiveLoginEvent $event
     */
    public function saveLocale(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        $this->session->set('_locale', $user->getLocale());
    }

    /**
     * Overrides current locale with one is saved in the session.
     *
     * @param   GetResponseEvent $event
     */
    public function setLocale(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // Override global locale with current user's one.
        if ($request->hasPreviousSession()) {
            $request->setLocale($request->getSession()->get('_locale', $this->locale));
        }
    }
}
