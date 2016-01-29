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
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class StickyLocaleTest extends BaseTestCase
{
    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    protected $request_stack;

    protected function setUp()
    {
        parent::setUp();

        $this->request_stack = $this->client->getContainer()->get('request_stack');
    }

    public function testSaveLocale()
    {
        $user = $this->findUser('artem');
        $user->setLocale('ru');

        $request = new Request();
        $token   = new UsernamePasswordToken($user, null, 'etraxis_provider');

        $event = new InteractiveLoginEvent($request, $token);

        $object = new StickyLocale($this->session, 'en');
        $object->saveLocale($event);

        $this->assertEquals('ru', $this->session->get('_locale'));
    }

    public function testSetDefaultLocale()
    {
        $request = new Request();

        $request->setSession($this->session);
        $request->cookies->set($this->session->getName(), $this->session->getId());

        $this->request_stack->push($request);

        $event = new GetResponseEvent(static::$kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $object = new StickyLocale($this->session, 'ru');

        $object->setLocale($event);

        $this->assertEquals('ru', $event->getRequest()->getLocale());
    }

    public function testSetLocaleBySession()
    {
        $request = new Request();

        $request->setSession($this->session);
        $request->cookies->set($this->session->getName(), $this->session->getId());
        $this->session->set('_locale', 'ja');

        $this->request_stack->push($request);

        $event = new GetResponseEvent(static::$kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $object = new StickyLocale($this->session, 'ru');

        $object->setLocale($event);

        $this->assertEquals('ja', $event->getRequest()->getLocale());
    }
}
