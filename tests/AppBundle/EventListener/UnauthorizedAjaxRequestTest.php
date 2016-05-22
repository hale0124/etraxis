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

use eTraxis\Tests\TransactionalTestCase;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class UnauthorizedAjaxRequestTest extends TransactionalTestCase
{
    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    protected $request_stack;

    /** @var \Symfony\Component\Security\Http\Authentication\AuthenticationUtils */
    protected $authentication_utils;

    /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface */
    protected $authorization_checker;

    /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface */
    protected $token_storage;

    protected function setUp()
    {
        parent::setUp();

        $this->request_stack         = $this->client->getContainer()->get('request_stack');
        $this->authentication_utils  = $this->client->getContainer()->get('security.authentication_utils');
        $this->authorization_checker = $this->client->getContainer()->get('security.authorization_checker');
        $this->token_storage         = $this->client->getContainer()->get('security.token_storage');
    }

    public function testHttpSuccessRequest()
    {
        $request  = new Request();
        $response = new Response();

        $this->request_stack->push($request);

        $event = new FilterResponseEvent(static::$kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $object = new UnauthorizedAjaxRequest($this->router, $this->translator, $this->authentication_utils);

        $object->onResponse($event);
        self::assertEquals(Response::HTTP_OK, $event->getResponse()->getStatusCode());
    }

    public function testHttpFailureRequest()
    {
        $request  = new Request();
        $response = new Response(null, Response::HTTP_FOUND);

        $response->headers->set('Location', $this->router->generate('login', [], Router::ABSOLUTE_URL));

        $this->request_stack->push($request);

        $event = new FilterResponseEvent(static::$kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $object = new UnauthorizedAjaxRequest($this->router, $this->translator, $this->authentication_utils);

        $object->onResponse($event);
        self::assertEquals(Response::HTTP_FOUND, $event->getResponse()->getStatusCode());
    }

    public function testAjaxSuccessRequest()
    {
        $request  = new Request();
        $response = new Response();

        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $this->request_stack->push($request);

        $event = new FilterResponseEvent(static::$kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $object = new UnauthorizedAjaxRequest($this->router, $this->translator, $this->authentication_utils);

        $object->onResponse($event);
        self::assertEquals(Response::HTTP_OK, $event->getResponse()->getStatusCode());
    }

    public function testAjaxFailureRequest()
    {
        $request  = new Request();
        $response = new Response(null, Response::HTTP_FOUND);

        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $response->headers->set('Location', $this->router->generate('login', [], Router::ABSOLUTE_URL));

        $this->request_stack->push($request);

        $event = new FilterResponseEvent(static::$kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $object = new UnauthorizedAjaxRequest($this->router, $this->translator, $this->authentication_utils);

        $object->onResponse($event);
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $event->getResponse()->getStatusCode());
    }
}
