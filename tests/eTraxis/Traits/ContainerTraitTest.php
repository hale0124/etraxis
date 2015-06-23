<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------


namespace AppBundle\Controller;

use eTraxis\Traits;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ControllerStub extends Controller
{
    use Traits\CallTrait;
    use Traits\ContainerTrait;
}

class ContainerTraitTest extends KernelTestCase
{
    private $object = null;

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

    public function testGetLogger()
    {
        $this->assertInstanceOf('\Psr\Log\LoggerInterface', $this->object->getLogger());
    }

    public function testGetCommandBus()
    {
        $this->assertInstanceOf('\SimpleBus\Message\Bus\MessageBus', $this->object->getCommandBus());
    }
}
