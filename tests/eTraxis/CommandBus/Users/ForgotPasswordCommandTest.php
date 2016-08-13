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

use eTraxis\Tests\TransactionalTestCase;
use eTraxis\Traits\ReflectionTrait;

class ForgotPasswordCommandTest extends TransactionalTestCase
{
    use ReflectionTrait;

    public function testSuccess()
    {
        $username = 'artem';

        $user = $this->findUser($username);

        $prevToken   = $this->getProperty($user, 'resetToken');
        $prevExpires = $this->getProperty($user, 'resetTokenExpiresAt');

        /** @var \eTraxis\Entity\User $user */
        self::assertTrue($user->isResetTokenExpired());

        $command = new ForgotPasswordCommand([
            'username' => $username,
            'ip'       => '127.0.0.1',
        ]);

        $this->commandbus->handle($command);

        $user = $this->findUser($username);

        self::assertNotEquals($prevToken, $this->getProperty($user, 'resetToken'));
        self::assertNotEquals($prevExpires, $this->getProperty($user, 'resetTokenExpiresAt'));

        /** @var \eTraxis\Entity\User $user */
        self::assertFalse($user->isResetTokenExpired());
    }
}
