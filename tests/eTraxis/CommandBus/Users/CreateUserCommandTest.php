<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Users;

use eTraxis\Entity\User;
use eTraxis\Tests\TransactionalTestCase;

class CreateUserCommandTest extends TransactionalTestCase
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
            'timezone'    => 'Asia/Vladivostok',
        ]);

        $this->commandbus->handle($command);

        $user = $this->findUser($username);

        self::assertInstanceOf(User::class, $user);
        self::assertEquals($username, $user->getUsername());
        self::assertEquals($fullname, $user->getFullname());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($description, $user->getDescription());
        self::assertEquals($encoded, $user->getPassword());
        self::assertTrue($user->isAdmin());
        self::assertFalse($user->isDisabled());
        self::assertFalse($user->isExternalAccount());
        self::assertEquals(static::$kernel->getContainer()->getParameter('locale'), $user->getLocale());
        self::assertEquals(static::$kernel->getContainer()->getParameter('theme'), $user->getTheme());
        self::assertEquals('Asia/Vladivostok', $user->getTimezone());
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
            'timezone' => 'UTC',
        ]);

        $this->commandbus->handle($command);
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
            'timezone' => 'UTC',
        ]);

        $this->commandbus->handle($command);
    }
}
