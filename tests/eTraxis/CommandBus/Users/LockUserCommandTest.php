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

class LockUserCommandTest extends BaseTestCase
{
    public function testLockUser()
    {
        $username = 'artem';

        $user = $this->findUser($username);
        $this->assertNotNull($user);

        $expected = $user->getAuthAttempts() + 1;

        $command = new LockUserCommand([
            'username' => $username,
        ]);

        // first time
        $this->command_bus->handle($command);

        $user = $this->findUser($username);
        $this->assertEquals($expected, $user->getAuthAttempts());

        // second time
        $this->command_bus->handle($command);

        $user = $this->findUser($username);
        $this->assertFalse($user->isAccountNonLocked());
    }
}
