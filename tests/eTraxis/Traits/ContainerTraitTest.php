<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Traits;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ControllerStub extends Controller
{
    use ClassAccessTrait;
    use ContainerTrait;
}

class ContainerTraitTest extends KernelTestCase
{
    /** @var ControllerStub */
    private $object;

    protected function setUp()
    {
        self::bootKernel();

        $this->object = new ControllerStub();
        $this->object->setContainer(static::$kernel->getContainer());
    }

    protected function tearDown()
    {
        unset($this->object);

        parent::tearDown();
    }

    public function testSetNotice()
    {
        /** @var \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface $flashBag */
        $flashBag = $this->object->container->get('session')->getFlashBag();
        $flashBag->clear();

        $this->object->setNotice('Information');

        $this->assertTrue($flashBag->has('notice'));
        $this->assertCount(1, $flashBag->get('notice'));
        $this->assertCount(0, $flashBag->get('notice'));
    }

    public function testSetError()
    {
        /** @var \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface $flashBag */
        $flashBag = $this->object->container->get('session')->getFlashBag();
        $flashBag->clear();

        $this->object->setError('Error');

        $this->assertTrue($flashBag->has('error'));
        $this->assertCount(1, $flashBag->get('error'));
        $this->assertCount(0, $flashBag->get('error'));
    }

    public function testTranslator()
    {
        $this->assertInstanceOf('\Symfony\Component\Translation\TranslatorInterface', $this->object->getTranslator());
    }

    public function testGetCommandBus()
    {
        $this->assertInstanceOf('\SimpleBus\Message\Bus\MessageBus', $this->object->getCommandBus());
    }

    public function testGetEventBus()
    {
        $this->assertInstanceOf('\SimpleBus\Message\Bus\MessageBus', $this->object->getEventBus());
    }

    public function testGetDataTables()
    {
        $this->assertInstanceOf('\DataTables\DataTablesInterface', $this->object->getDataTables());
    }
}
