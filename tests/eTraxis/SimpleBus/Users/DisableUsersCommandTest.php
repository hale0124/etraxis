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

class DisableUsersCommandTest extends BaseTestCase
{
    public function testDisableUser()
    {
        $this->loginAs('hubert');

        $usernames = ['hubert', 'zapp', 'kif'];

        $ids = [];

        foreach ($usernames as $username) {
            $user = $this->findUser($username);

            $this->assertNotNull($user);
            $this->assertFalse($user->isDisabled());

            $ids[] = $user->getId();
        }

        $command = new DisableUsersCommand(['ids' => $ids]);

        $this->command_bus->handle($command);

        $this->doctrine->getManager()->clear();

        foreach ($usernames as $username) {
            $user = $this->findUser($username);

            if ($user->getUsername() == 'hubert') {
                $this->assertFalse($user->isDisabled());
            }
            else {
                $this->assertTrue($user->isDisabled());
            }
        }
    }
}
