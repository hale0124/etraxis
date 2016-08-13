<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Middleware;

use eTraxis\Tests\ControllerTestCase;
use eTraxis\Traits\MessageStub;

class ValidationMiddlewareTest extends ControllerTestCase
{
    public function testSuccess()
    {
        $message = new MessageStub([
            'property' => 10,
        ]);

        $this->expectOutputString('This point must be reached.');

        $middleware = new ValidationMiddleware($this->validator);
        $middleware->handle($message, function () {
            echo 'This point must be reached.';
        });
    }

    public function testFailure()
    {
        $message = new MessageStub([
            'property' => 0,
            'name'     => str_repeat('*', 100),
        ]);

        try {
            $middleware = new ValidationMiddleware($this->validator);
            $middleware->handle($message, function () {
                self::fail('This point should not be reached.');
            });
        }
        catch (ValidationException $e) {

            $errors = $e->toArray();

            self::assertCount(2, $errors);
            self::assertEquals('This value should be "1" or more.', $errors['property']);
            self::assertEquals('This value is too long. It should have 10 characters or less.', $errors['name']);

            return;
        }

        self::fail('This point should not be reached.');
    }
}
