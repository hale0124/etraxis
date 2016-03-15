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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CsrfTokenTest extends BaseTestCase
{
    /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface */
    protected $tokens;

    /** @var CsrfToken */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->tokens = $this->client->getContainer()->get('security.csrf.token_manager');

        $this->object = new CsrfToken($this->logger, $this->tokens);
    }

    public function testSimpleRequest()
    {
        $this->tokens->refreshToken('');

        $formdata = [
            '_token' => $this->tokens->getToken('')->getValue(),
            'fname'  => 'Artem',
            'lname'  => 'Rodygin',
        ];

        $request = new Request([], $formdata);
        $request->setMethod(Request::METHOD_POST);
        $request->headers->add(['X-Requested-With' => 'XMLHttpRequest']);

        $event = new GetResponseEvent(static::$kernel, $request, HttpKernelInterface::SUB_REQUEST);

        $this->object->checkCsrfToken($event);

        $response = $event->getResponse();

        $this->assertNull($response);
    }

    public function testFormSubmit()
    {
        $this->tokens->refreshToken('user');

        $formdata = [
            'user' => [
                '_token' => $this->tokens->getToken('user')->getValue(),
                'fname'  => 'Artem',
                'lname'  => 'Rodygin',
            ],
        ];

        $request = new Request([], $formdata);
        $request->setMethod(Request::METHOD_POST);
        $request->headers->add(['X-Requested-With' => 'XMLHttpRequest']);

        $event = new GetResponseEvent(static::$kernel, $request, HttpKernelInterface::SUB_REQUEST);

        $this->object->checkCsrfToken($event);

        $response = $event->getResponse();

        $this->assertNull($response);
    }

    public function testNoCsrfError()
    {
        $formdata = [
            'fname' => 'Artem',
            'lname' => 'Rodygin',
        ];

        $request = new Request([], $formdata);
        $request->setMethod(Request::METHOD_POST);
        $request->headers->add(['X-Requested-With' => 'XMLHttpRequest']);

        $event = new GetResponseEvent(static::$kernel, $request, HttpKernelInterface::SUB_REQUEST);

        $this->object->checkCsrfToken($event);

        $response = $event->getResponse();

        $this->assertNotNull($response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals('CSRF token is missing.', $response->getContent());
    }

    public function testInvalidCsrfError()
    {
        $this->tokens->refreshToken('');
        $this->tokens->refreshToken('user');

        $formdata = [
            '_token' => $this->tokens->getToken('user')->getValue(),
            'fname'  => 'Artem',
            'lname'  => 'Rodygin',
        ];

        $request = new Request([], $formdata);
        $request->setMethod(Request::METHOD_POST);
        $request->headers->add(['X-Requested-With' => 'XMLHttpRequest']);

        $event = new GetResponseEvent(static::$kernel, $request, HttpKernelInterface::SUB_REQUEST);

        $this->object->checkCsrfToken($event);

        $response = $event->getResponse();

        $this->assertNotNull($response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals('Invalid CSRF token.', $response->getContent());
    }
}
