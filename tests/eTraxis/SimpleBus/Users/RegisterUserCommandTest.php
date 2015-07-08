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

namespace eTraxis\SimpleBus\Users;

use eTraxis\Tests\BaseTestCase;

class RegisterUserCommandTest extends BaseTestCase
{
    public function testRegisterUser()
    {
        $username = 'anna';
        $fullname = 'Anna Rodygina';
        $email    = 'anna@example.com';
        $locale   = static::$kernel->getContainer()->getParameter('locale');
        $theme    = static::$kernel->getContainer()->getParameter('theme');

        $user = $this->doctrine->getRepository('eTraxis:User')->findOneBy([
            'username' => $username,
            'isLdap'   => true,
        ]);

        $this->assertNull($user);

        // first time
        $command = new RegisterUserCommand([
            'username' => $username,
            'fullname' => $fullname,
            'email'    => $email,
        ]);

        $this->command_bus->handle($command);

        /** @var \eTraxis\Model\User $user */
        $user = $this->doctrine->getRepository('eTraxis:User')->findOneBy([
            'username' => $username,
            'isLdap'   => true,
        ]);

        $id = $user->getId();

        $this->assertInstanceOf('eTraxis\Model\User', $user);
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($fullname, $user->getFullname());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($locale, $user->getLocale());
        $this->assertEquals($theme, $user->getTheme());
        $this->assertTrue($user->isLdap());
        $this->assertEquals($id, $command->id);

        // second time
        $command = new RegisterUserCommand([
            'username' => $username,
            'fullname' => $fullname,
            'email'    => $email,
        ]);

        $this->command_bus->handle($command);

        /** @var \eTraxis\Model\User $user */
        $user = $this->doctrine->getRepository('eTraxis:User')->findOneBy([
            'username' => $username,
            'isLdap'   => true,
        ]);

        $this->assertInstanceOf('eTraxis\Model\User', $user);
        $this->assertEquals($id, $user->getId());
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($fullname, $user->getFullname());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($locale, $user->getLocale());
        $this->assertEquals($theme, $user->getTheme());
        $this->assertTrue($user->isLdap());
        $this->assertEquals($id, $command->id);
    }

    /**
     * @expectedException     \eTraxis\Exception\ResponseException
     * @expectedExceptionCode 400
     */
    public function testBadRequest()
    {
        $command = new RegisterUserCommand();

        $this->command_bus->handle($command);
    }
}