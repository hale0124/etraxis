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


namespace AppBundle\EventListener;

use eTraxis\Tests\BaseTestCase;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class KernelListenerTest extends BaseTestCase
{
    public function testSetDefaultLocale()
    {
        $request = new Request();

        $request->setSession($this->session);
        $request->cookies->set($this->session->getName(), $this->session->getId());

        $event = new GetResponseEvent(static::$kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $object = new KernelListener($this->router, $this->translator, 'ru');
        $object->onKernelRequest($event);

        $this->assertEquals('ru', $event->getRequest()->getLocale());
    }

    public function testSetLocaleByRequest()
    {
        $request = new Request();

        $request->setSession($this->session);
        $request->cookies->set($this->session->getName(), $this->session->getId());
        $request->attributes->set('_locale', 'ja');

        $event = new GetResponseEvent(static::$kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $object = new KernelListener($this->router, $this->translator, 'ru');
        $object->onKernelRequest($event);

        $request->attributes->remove('_locale');
        $object->onKernelRequest($event);

        $this->assertEquals('ja', $event->getRequest()->getLocale());
    }

    public function testHttpSuccessRequest()
    {
        $request  = new Request();
        $response = new Response();

        $event = new FilterResponseEvent(static::$kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $object = new KernelListener($this->router, $this->translator, 'en');

        $object->onKernelResponse($event);
        $this->assertEquals(Response::HTTP_OK, $event->getResponse()->getStatusCode());
    }

    public function testHttpFailureRequest()
    {
        $request  = new Request();
        $response = new Response(null, Response::HTTP_FOUND);

        $response->headers->set('Location', $this->router->generate('login', [], Router::ABSOLUTE_URL));

        $event = new FilterResponseEvent(static::$kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $object = new KernelListener($this->router, $this->translator, 'en');

        $object->onKernelResponse($event);
        $this->assertEquals(Response::HTTP_FOUND, $event->getResponse()->getStatusCode());
    }

    public function testAjaxSuccessRequest()
    {
        $request  = new Request();
        $response = new Response();

        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $event = new FilterResponseEvent(static::$kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $object = new KernelListener($this->router, $this->translator, 'en');

        $object->onKernelResponse($event);
        $this->assertEquals(Response::HTTP_OK, $event->getResponse()->getStatusCode());
    }

    public function testAjaxFailureRequest()
    {
        $request  = new Request();
        $response = new Response(null, Response::HTTP_FOUND);

        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $response->headers->set('Location', $this->router->generate('login', [], Router::ABSOLUTE_URL));

        $event = new FilterResponseEvent(static::$kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $object = new KernelListener($this->router, $this->translator, 'en');

        $object->onKernelResponse($event);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $event->getResponse()->getStatusCode());
    }
}
