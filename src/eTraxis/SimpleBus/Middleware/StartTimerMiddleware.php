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


namespace eTraxis\SimpleBus\Middleware;

use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Middleware to start processing timer.
 */
class StartTimerMiddleware implements MessageBusMiddleware
{
    protected $session;

    /**
     * Dependency Injection constructor.
     *
     * @param   SessionInterface $session User's session.
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritDoc}
     */
    public function handle($message, callable $next)
    {
        list($msec, $sec) = explode(' ', microtime());

        $timer = (float) $msec + (float) $sec;
        $this->session->set('eTraxis.timer', $timer);

        $next($message);
    }
}
