<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users;

use eTraxis\Entity\User;
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

        self::assertNull($user);

        // first time
        $command = new RegisterUserCommand([
            'username' => $username,
            'fullname' => $fullname,
            'email'    => $email,
        ]);

        $this->command_bus->handle($command);

        $user = $this->findUser($username, true);

        $id = $user->getId();

        self::assertInstanceOf(User::class, $user);
        self::assertEquals($username, $user->getUsername());
        self::assertEquals($fullname, $user->getFullname());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($locale, $user->getSettings()->getLocale());
        self::assertEquals($theme, $user->getSettings()->getTheme());
        self::assertTrue($user->isLdap());

        // second time
        $command = new RegisterUserCommand([
            'username' => $username,
            'fullname' => $fullname,
            'email'    => $email,
        ]);

        $this->command_bus->handle($command);

        $user = $this->findUser($username, true);

        self::assertInstanceOf(User::class, $user);
        self::assertEquals($id, $user->getId());
        self::assertEquals($username, $user->getUsername());
        self::assertEquals($fullname, $user->getFullname());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($locale, $user->getSettings()->getLocale());
        self::assertEquals($theme, $user->getSettings()->getTheme());
        self::assertTrue($user->isLdap());
    }

    /**
     * @expectedException \SimpleBus\ValidationException
     * @expectedExceptionMessage This value should not be blank.
     */
    public function testBadRequest()
    {
        $command = new RegisterUserCommand();

        $this->command_bus->handle($command);
    }
}
