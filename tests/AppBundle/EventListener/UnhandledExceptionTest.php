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

use eTraxis\Tests\BaseTestCase;
use SimpleBus\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class UnhandledExceptionTest extends BaseTestCase
{
    /** @var UnhandledException */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new UnhandledException($this->logger);
    }

    public function testMasterRequest()
    {
        $request = new Request();

        $event = new GetResponseForExceptionEvent(
            static::$kernel,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new HttpException(Response::HTTP_NOT_FOUND, 'Unknown username.')
        );

        $this->object->onException($event);

        $response = $event->getResponse();

        self::assertNull($response);
    }

    public function testValidationException()
    {
        $errors = [
            'username' => 'Invalid username.',
            'password' => 'The field is required.',
        ];

        $request = new Request();
        $request->headers->add(['X-Requested-With' => 'XMLHttpRequest']);

        $event = new GetResponseForExceptionEvent(
            static::$kernel,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new ValidationException($errors)
        );

        $this->object->onException($event);

        $response = $event->getResponse();
        $content  = $response->getContent();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertEquals(json_encode($errors), $content);
    }

    public function testHttpException()
    {
        $request = new Request();
        $request->headers->add(['X-Requested-With' => 'XMLHttpRequest']);

        $event = new GetResponseForExceptionEvent(
            static::$kernel,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new AccessDeniedHttpException('You are not allowed for this action.')
        );

        $this->object->onException($event);

        $response = $event->getResponse();
        $content  = $response->getContent();

        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        self::assertEquals('"You are not allowed for this action."', $content);
    }

    public function testException()
    {
        $request = new Request();
        $request->headers->add(['X-Requested-With' => 'XMLHttpRequest']);

        $event = new GetResponseForExceptionEvent(
            static::$kernel,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new \Exception('Something went wrong.')
        );

        $this->object->onException($event);

        $response = $event->getResponse();
        $content  = $response->getContent();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertEquals('"Something went wrong."', $content);
    }
}
