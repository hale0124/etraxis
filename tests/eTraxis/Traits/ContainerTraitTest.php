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

use AltrEgo\AltrEgo;
use AppBundle\Controller\Web\DefaultController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContainerTraitTest extends KernelTestCase
{
    /** @var DefaultController */
    private $object;

    protected function setUp()
    {
        self::bootKernel();

        $this->object = AltrEgo::create(new DefaultController());
        $this->object->setContainer(static::$kernel->getContainer());
    }

    protected function tearDown()
    {
        unset($this->object);

        parent::tearDown();
    }

    public function testGetCommandBus()
    {
        self::assertInstanceOf('\SimpleBus\Message\Bus\MessageBus', $this->object->getCommandBus());
    }

    public function testGetEventBus()
    {
        self::assertInstanceOf('\SimpleBus\Message\Bus\MessageBus', $this->object->getEventBus());
    }

    public function testGetDataTables()
    {
        self::assertInstanceOf('\DataTables\DataTablesInterface', $this->object->getDataTables());
    }
}
