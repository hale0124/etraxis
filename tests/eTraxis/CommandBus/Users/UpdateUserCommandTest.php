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

class UpdateUserCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        $this->loginAs('hubert');

        $user = $this->findUser('bender');

        self::assertNotEmpty($user->getDescription());
        self::assertFalse($user->isAdmin());
        self::assertFalse($user->isDisabled());
        self::assertEquals('en_US', $user->getLocale());
        self::assertEquals('azure', $user->getTheme());
        self::assertEquals('UTC', $user->getTimezone());

        $command = new UpdateUserCommand([
            'id'       => $user->getId(),
            'username' => 'flexo',
            'fullname' => 'Flexo',
            'email'    => 'flexo@example.com',
            'admin'    => true,
            'disabled' => true,
            'locale'   => 'es',
            'theme'    => 'humanity',
            'timezone' => 'Asia/Vladivostok',
        ]);

        $this->command_bus->handle($command);

        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->find($user->getId());

        self::assertEquals('flexo', $user->getUsername());
        self::assertEquals('Flexo', $user->getFullname());
        self::assertEquals('flexo@example.com', $user->getEmail());
        self::assertEmpty($user->getDescription());
        self::assertTrue($user->isAdmin());
        self::assertTrue($user->isDisabled());
        self::assertEquals('es', $user->getLocale());
        self::assertEquals('humanity', $user->getTheme());
        self::assertEquals('Asia/Vladivostok', $user->getTimezone());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown user.
     */
    public function testUnknownUser()
    {
        $this->loginAs('hubert');

        $user = $this->findUser('flexo');

        self::assertNull($user);

        $command = new UpdateUserCommand([
            'id'       => self::UNKNOWN_ENTITY_ID,
            'username' => 'flexo',
            'fullname' => 'Flexo',
            'email'    => 'flexo@example.com',
            'admin'    => true,
            'disabled' => true,
            'locale'   => static::$kernel->getContainer()->getParameter('locale'),
            'theme'    => static::$kernel->getContainer()->getParameter('theme'),
            'timezone' => 'UTC',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Account with entered user name already exists.
     */
    public function testUsernameConflict()
    {
        $this->loginAs('hubert');

        $user = $this->findUser('hubert');

        $command = new UpdateUserCommand([
            'id'       => $user->getId(),
            'username' => 'bender',
            'fullname' => $user->getFullname(),
            'email'    => $user->getEmail(),
            'admin'    => $user->isAdmin(),
            'disabled' => $user->isDisabled(),
            'locale'   => static::$kernel->getContainer()->getParameter('locale'),
            'theme'    => static::$kernel->getContainer()->getParameter('theme'),
            'timezone' => 'UTC',
        ]);

        $this->command_bus->handle($command);
    }
}
