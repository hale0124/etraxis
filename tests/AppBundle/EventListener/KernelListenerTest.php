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
use Symfony\Component\HttpFoundation\Request;
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
}
