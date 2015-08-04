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

namespace eTraxis\Traits;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class ControllerStub extends Controller
{
    use ClassAccessTrait;
    use ContainerTrait;
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

    public function testGetFormDataSuccessGet()
    {
        /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrf */
        $csrf = static::$kernel->getContainer()->get('security.csrf.token_manager');
        $csrf->refreshToken('form');

        $formdata = [
            '_token' => $csrf->getToken('form')->getValue(),
            'fname'  => 'Artem',
            'lname'  => 'Rodygin',
        ];

        $request = new Request(['form' => $formdata]);

        $this->assertEquals($formdata, $this->object->getFormData($request));
    }

    public function testGetFormDataSuccessPost()
    {
        /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrf */
        $csrf = static::$kernel->getContainer()->get('security.csrf.token_manager');
        $csrf->refreshToken('user');

        $formdata = [
            '_token' => $csrf->getToken('user')->getValue(),
            'fname'  => 'Artem',
            'lname'  => 'Rodygin',
        ];

        $request = new Request([], ['user' => $formdata]);
        $request->setMethod(Request::METHOD_POST);

        $this->assertEquals($formdata, $this->object->getFormData($request, 'user'));
    }

    /**
     * @expectedException \eTraxis\CommandBus\CommandException
     * @expectedExceptionMessage No data submitted.
     */
    public function testGetFormDataNoData()
    {
        /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrf */
        $csrf = static::$kernel->getContainer()->get('security.csrf.token_manager');
        $csrf->refreshToken('user');

        $formdata = [
            '_token' => $csrf->getToken('user')->getValue(),
            'fname'  => 'Artem',
            'lname'  => 'Rodygin',
        ];

        $request = new Request(['user' => $formdata]);

        $this->object->getFormData($request);
    }

    /**
     * @expectedException \eTraxis\CommandBus\CommandException
     * @expectedExceptionMessage Invalid CSRF token.
     */
    public function testGetFormDataInvalidCsrf()
    {
        /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrf */
        $csrf = static::$kernel->getContainer()->get('security.csrf.token_manager');
        $csrf->refreshToken('user');
        $csrf->refreshToken('form');

        $formdata = [
            '_token' => $csrf->getToken('user')->getValue(),
            'fname'  => 'Artem',
            'lname'  => 'Rodygin',
        ];

        $request = new Request(['form' => $formdata]);

        $this->object->getFormData($request);
    }

    /**
     * @expectedException \eTraxis\CommandBus\CommandException
     * @expectedExceptionMessage CSRF token is missing.
     */
    public function testGetFormDataNoCsrf()
    {
        $formdata = [
            'fname'  => 'Artem',
            'lname'  => 'Rodygin',
        ];

        $request = new Request(['form' => $formdata]);

        $this->object->getFormData($request);
    }

    public function testGetAuthorizationChecker()
    {
        $this->assertInstanceOf('\Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface', $this->object->getAuthorizationChecker());
    }

    public function testTranslator()
    {
        $this->assertInstanceOf('\Symfony\Component\Translation\TranslatorInterface', $this->object->getTranslator());
    }

    public function testValidator()
    {
        $this->assertInstanceOf('\Symfony\Component\Validator\Validator\ValidatorInterface', $this->object->getValidator());
    }

    public function testGetCommandBus()
    {
        $this->assertInstanceOf('\eTraxis\CommandBus\CommandBusInterface', $this->object->getCommandBus());
    }
}
