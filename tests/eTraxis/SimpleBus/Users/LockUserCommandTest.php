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

use eTraxis\Tests\BaseTestCase;

class LockUserCommandTest extends BaseTestCase
{
    public function testLockUser()
    {
        $username = 'artem';

        $user = $this->findUser($username);
        self::assertNotNull($user);

        $command = new LockUserCommand([
            'username' => $username,
        ]);

        // first time
        $this->command_bus->handle($command);

        $user = $this->findUser($username);
        self::assertFalse($user->isLocked());

        // second time
        $this->command_bus->handle($command);

        $user = $this->findUser($username);
        self::assertTrue($user->isLocked());
    }
}
