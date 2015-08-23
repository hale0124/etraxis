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

namespace eTraxis\CommandBus\Users;

use eTraxis\Tests\BaseTestCase;

class ForgotPasswordCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        $username = 'artem';

        $user = $this->findUser($username);
        $this->assertNotNull($user);

        $prevToken   = $user->getResetToken();
        $prevExpires = $user->getResetTokenExpiresAt();

        $command = new ForgotPasswordCommand([
            'username' => $username,
            'ip'       => '127.0.0.1',
        ]);

        $this->command_bus->handle($command);

        $user = $this->findUser($username);

        $this->assertNotEquals($prevToken, $user->getResetToken());
        $this->assertNotEquals($prevExpires, $user->getResetTokenExpiresAt());
        $this->assertGreaterThan(time(), $user->getResetTokenExpiresAt());
    }
}
