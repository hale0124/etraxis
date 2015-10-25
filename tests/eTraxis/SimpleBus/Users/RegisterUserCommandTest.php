<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
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

        $user = $this->findUser($username, true);

        $this->assertNull($user);

        // first time
        $command = new RegisterUserCommand([
            'username' => $username,
            'fullname' => $fullname,
            'email'    => $email,
        ]);

        $this->command_bus->handle($command);

        $user = $this->findUser($username, true);

        $id = $user->getId();

        $this->assertInstanceOf('eTraxis\Entity\User', $user);
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($fullname, $user->getFullname());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($locale, $user->getLocale());
        $this->assertEquals($theme, $user->getTheme());
        $this->assertTrue($user->isLdap());

        // second time
        $command = new RegisterUserCommand([
            'username' => $username,
            'fullname' => $fullname,
            'email'    => $email,
        ]);

        $this->command_bus->handle($command);

        $user = $this->findUser($username, true);

        $this->assertInstanceOf('eTraxis\Entity\User', $user);
        $this->assertEquals($id, $user->getId());
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($fullname, $user->getFullname());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($locale, $user->getLocale());
        $this->assertEquals($theme, $user->getTheme());
        $this->assertTrue($user->isLdap());
    }

    /**
     * @expectedException     \eTraxis\SimpleBus\ValidationException
     * @expectedExceptionCode 400
     */
    public function testBadRequest()
    {
        $command = new RegisterUserCommand();

        $this->command_bus->handle($command);
    }
}
