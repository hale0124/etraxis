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

use eTraxis\Dictionary\AuthenticationProvider;
use eTraxis\Entity\User;
use eTraxis\Tests\TransactionalTestCase;

class RegisterUserCommandTest extends TransactionalTestCase
{
    public function testRegisterUser()
    {
        $username = 'anna';
        $fullname = 'Anna Rodygina';
        $email    = 'anna@example.com';
        $locale   = static::$kernel->getContainer()->getParameter('locale');
        $theme    = static::$kernel->getContainer()->getParameter('theme');

        $user = $this->findUser($username, AuthenticationProvider::LDAP);

        self::assertNull($user);

        // first time
        $command = new RegisterUserCommand([
            'username' => $username,
            'fullname' => $fullname,
            'email'    => $email,
        ]);

        $this->commandbus->handle($command);

        $user = $this->findUser($username, AuthenticationProvider::LDAP);

        $id = $user->getId();

        self::assertInstanceOf(User::class, $user);
        self::assertEquals($username, $user->getUsername());
        self::assertEquals($fullname, $user->getFullname());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($locale, $user->getLocale());
        self::assertEquals($theme, $user->getTheme());
        self::assertTrue($user->isExternalAccount());

        // second time
        $command = new RegisterUserCommand([
            'username' => $username,
            'fullname' => $fullname,
            'email'    => $email,
        ]);

        $this->commandbus->handle($command);

        $user = $this->findUser($username, AuthenticationProvider::LDAP);

        self::assertInstanceOf(User::class, $user);
        self::assertEquals($id, $user->getId());
        self::assertEquals($username, $user->getUsername());
        self::assertEquals($fullname, $user->getFullname());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($locale, $user->getLocale());
        self::assertEquals($theme, $user->getTheme());
        self::assertTrue($user->isExternalAccount());
    }

    /**
     * @expectedException \eTraxis\CommandBus\Middleware\ValidationException
     * @expectedExceptionMessage This value should not be blank.
     */
    public function testBadRequest()
    {
        $command = new RegisterUserCommand();

        $this->commandbus->handle($command);
    }
}
