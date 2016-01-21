<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Middleware;

use Psr\Log\LoggerInterface;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Middleware to calculate final processing time.
 */
class StopTimerMiddleware implements MessageBusMiddleware
{
    protected $logger;
    protected $session;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface  $logger
     * @param   SessionInterface $session
     */
    public function __construct(LoggerInterface $logger, SessionInterface $session)
    {
        $this->logger  = $logger;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
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
