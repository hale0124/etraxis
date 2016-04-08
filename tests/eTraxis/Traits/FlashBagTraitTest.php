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
use AppBundle\Controller\Web\SecurityController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FlashBagTraitTest extends KernelTestCase
{
    /** @var SecurityController */
    private $object;

    protected function setUp()
    {
        self::bootKernel();

        $this->object = AltrEgo::create(new SecurityController());
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

        self::assertTrue($flashBag->has('notice'));
        self::assertCount(1, $flashBag->get('notice'));
        self::assertCount(0, $flashBag->get('notice'));
    }

    public function testSetError()
    {
        /** @var \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface $flashBag */
        $flashBag = $this->object->container->get('session')->getFlashBag();
        $flashBag->clear();

        $this->object->setError('Error');

        self::assertTrue($flashBag->has('error'));
        self::assertCount(1, $flashBag->get('error'));
        self::assertCount(0, $flashBag->get('error'));
    }
}
