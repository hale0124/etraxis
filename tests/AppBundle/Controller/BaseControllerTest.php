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

use eTraxis\Tests\ClassAccessTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BaseControllerStub extends BaseController
{
    use ClassAccessTrait;
}

class BaseControllerTest extends KernelTestCase
{
    /** @var BaseController */
    private $object = null;

    protected function setUp()
    {
        self::bootKernel();

        $this->object = new BaseControllerStub();
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

    public function testGetAuthorizationChecker()
    {
        $this->assertInstanceOf('\Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface', $this->object->getAuthorizationChecker());
    }

    public function testGetSession()
    {
        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Session\SessionInterface', $this->object->getSession());
    }

    public function testGetTranslator()
    {
        $this->assertInstanceOf('\Symfony\Component\Translation\TranslatorInterface', $this->object->getTranslator());
    }

    public function testGetValidator()
    {
        $this->assertInstanceOf('\Symfony\Component\Validator\Validator\ValidatorInterface', $this->object->getValidator());
    }

    public function testGetObjectManager()
    {
        $this->assertInstanceOf('\Doctrine\Common\Persistence\ObjectManager', $this->object->getObjectManager());
    }

    public function testGetTwig()
    {
        $this->assertInstanceOf('\Twig_Environment', $this->object->getTwig());
    }

    public function testGetMailer()
    {
        $this->assertInstanceOf('\eTraxis\Service\MailerService', $this->object->getMailer());
    }
}
