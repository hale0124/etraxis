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

/**
 * Middleware to calculate message processing time.
 */
class TimingMiddleware implements MessageBusMiddleware
{
    protected $logger;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($message, callable $next)
    {
        $start = microtime(true);

        $next($message);

        $stop = microtime(true);

        $this->logger->debug('Message processing time', [$stop - $start]);
    }
}
