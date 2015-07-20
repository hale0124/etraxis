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

namespace eTraxis\SimpleBus\Users;

use eTraxis\Tests\BaseTestCase;

class UnlockUserCommandTest extends BaseTestCase
{
    public function testUnlockUser()
    {
        $username = 'artem';

        $user = $this->findUser($username);
        $this->assertNotNull($user);

        $user->setAuthAttempts(1);

        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();

        $command = new UnlockUserCommand([
            'username' => $username,
        ]);

        $this->command_bus->handle($command);

        $user = $this->findUser($username);
        $this->assertEquals(0, $user->getAuthAttempts());
    }
}
