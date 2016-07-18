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

use Symfony\Component\HttpKernel\Tests\Logger;

class TimingMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    public function testTiming()
    {
        $logger  = new Logger();
        $message = new \stdClass();

        $middleware = new TimingMiddleware($logger);
        $middleware->handle($message, function () {
        });

        self::assertContains('Message processing time', $logger->getLogs('debug'));
    }
}
