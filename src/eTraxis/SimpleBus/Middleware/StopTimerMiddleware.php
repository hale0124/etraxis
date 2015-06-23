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

use Psr\Log\LoggerInterface;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Middleware to calculate overall processing timer.
 */
class StopTimerMiddleware implements MessageBusMiddleware
{
    protected $logger;
    protected $session;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface  $logger  Debug logger.
     * @param   SessionInterface $session User's session.
     */
    public function __construct(LoggerInterface $logger, SessionInterface $session)
    {
        $this->logger  = $logger;
        $this->session = $session;
    }

    /**
     * {@inheritDoc}
     */
    public function handle($message, callable $next)
    {
        $next($message);

        list($msec, $sec) = explode(' ', microtime());

        $timer = (float) $msec + (float) $sec;
        $timer -= $this->session->get('eTraxis.timer');

        $this->logger->debug('Message processing time', [$timer]);
    }
}
