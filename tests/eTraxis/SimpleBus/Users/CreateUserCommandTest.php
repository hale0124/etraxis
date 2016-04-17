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

class CreateUserCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        $username    = 'anna';
        $fullname    = 'Anna Rodygina';
        $email       = 'anna@example.com';
        $description = 'Very lovely Daughter';
        $password    = 'secret';
        $encoded     = '5en6G6MezRroT3XKqkdPOmY/BfQ=';
        $admin       = true;
        $disabled    = false;

        $user = $this->findUser($username);

        self::assertNull($user);

        $command = new CreateUserCommand([
            'username'    => $username,
            'fullname'    => $fullname,
            'email'       => $email,
            'description' => $description,
            'password'    => $password,
            'admin'       => $admin,
            'disabled'    => $disabled,
            'locale'      => static::$kernel->getContainer()->getParameter('locale'),
            'theme'       => static::$kernel->getContainer()->getParameter('theme'),
            'timezone'    => 377,
        ]);

        $this->command_bus->handle($command);

        $user = $this->findUser($username);

        self::assertInstanceOf(User::class, $user);
        self::assertEquals($username, $user->getUsername());
        self::assertEquals($fullname, $user->getFullname());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($description, $user->getDescription());
        self::assertEquals($encoded, $user->getPassword());
        self::assertTrue($user->isAdmin());
        self::assertFalse($user->isDisabled());
        self::assertFalse($user->isLdap());
        self::assertEquals(static::$kernel->getContainer()->getParameter('locale'), $user->getSettings()->getLocale());
        self::assertEquals(static::$kernel->getContainer()->getParameter('theme'), $user->getSettings()->getTheme());
        self::assertEquals(377, $user->getSettings()->getTimezone());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Password should be at least 6 characters length.
     */
    public function testPasswordTooShort()
    {
        $command = new CreateUserCommand([
            'username' => 'anna',
            'fullname' => 'Anna Rodygina',
            'email'    => 'anna@example.com',
            'password' => 'short',
            'admin'    => true,
            'disabled' => false,
            'locale'   => static::$kernel->getContainer()->getParameter('locale'),
            'theme'    => static::$kernel->getContainer()->getParameter('theme'),
            'timezone' => 0,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Account with entered user name already exists.
     */
    public function testUsernameConflict()
    {
        $command = new CreateUserCommand([
            'username' => 'artem',
            'fullname' => 'Artem Rodygin',
            'email'    => 'artem@example.com',
            'password' => 'secret',
            'admin'    => true,
            'disabled' => false,
            'locale'   => static::$kernel->getContainer()->getParameter('locale'),
            'theme'    => static::$kernel->getContainer()->getParameter('theme'),
            'timezone' => 0,
        ]);

        $this->command_bus->handle($command);
    }
}
