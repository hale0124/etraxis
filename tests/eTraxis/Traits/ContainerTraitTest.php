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

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

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
