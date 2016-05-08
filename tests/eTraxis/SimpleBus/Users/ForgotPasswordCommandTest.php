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

use AltrEgo\AltrEgo;
use eTraxis\Tests\BaseTestCase;

class ForgotPasswordCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        $username = 'artem';

        /** @var \StdClass $user */
        $user = AltrEgo::create($this->findUser($username));

        $prevToken   = $user->resetToken;
        $prevExpires = $user->resetTokenExpiresAt;

        /** @var \eTraxis\Entity\User $user */
        self::assertTrue($user->isResetTokenExpired());

        $command = new ForgotPasswordCommand([
            'username' => $username,
            'ip'       => '127.0.0.1',
        ]);

        $this->command_bus->handle($command);

        /** @var \StdClass $user */
        $user = AltrEgo::create($this->findUser($username));

        self::assertNotEquals($prevToken, $user->resetToken);
        self::assertNotEquals($prevExpires, $user->resetTokenExpiresAt);

        /** @var \eTraxis\Entity\User $user */
        self::assertFalse($user->isResetTokenExpired());
    }
}
