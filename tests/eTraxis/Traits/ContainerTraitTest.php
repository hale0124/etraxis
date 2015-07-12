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
use Symfony\Component\Form\FormError;

class ControllerStub extends Controller
{
    use Traits\CallTrait;
    use Traits\ContainerTrait;
}

class ContainerTraitTest extends KernelTestCase
{
    /** @var ControllerStub */
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

    public function testGetFormErrorEmpty()
    {
        $form = $this->object->createFormBuilder()->getForm();

        $this->assertNull($this->object->getFormError($form));
    }

    public function testGetFormErrorExisting()
    {
        $form = $this->object->createFormBuilder(null, ['label' => 'Caption'])
            ->add('test', 'text')
            ->getForm();

        $error = new FormError('Error message');
        $error->setOrigin($form);

        $form->addError($error);

        $expected = sprintf('<p class="field-error">%s</p>%s', 'Caption', 'Error message');

        $this->assertEquals($expected, $this->object->getFormError($form));
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
