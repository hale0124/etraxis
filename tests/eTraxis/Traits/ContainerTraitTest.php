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

use AppBundle\Controller\Web\SecurityController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContainerTraitTest extends KernelTestCase
{
    use ReflectionTrait;

    /** @var SecurityController */
    private $object;

    protected function setUp()
    {
        self::bootKernel();

        $this->object = new SecurityController();
        $this->callMethod($this->object, 'setContainer', [static::$kernel->getContainer()]);
    }

    protected function tearDown()
    {
        unset($this->object);

        parent::tearDown();
    }

    public function testGetCommandBus()
    {
        $object = $this->callMethod($this->object, 'getCommandBus');
        self::assertInstanceOf('\SimpleBus\Message\Bus\MessageBus', $object);
    }

    public function testGetEventBus()
    {
        $object = $this->callMethod($this->object, 'getEventBus');
        self::assertInstanceOf('\SimpleBus\Message\Bus\MessageBus', $object);
    }
}
