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

class DisableUsersCommandTest extends BaseTestCase
{
    public function testDisableUser()
    {
        $this->loginAs('hubert');

        $usernames = ['hubert', 'zapp', 'kif'];

        $ids = [];

        foreach ($usernames as $username) {
            $user = $this->findUser($username);

            self::assertNotNull($user);
            self::assertFalse($user->isDisabled());

            $ids[] = $user->getId();
        }

        $command = new DisableUsersCommand(['ids' => $ids]);

        $this->command_bus->handle($command);

        $this->doctrine->getManager()->clear();

        foreach ($usernames as $username) {
            $user = $this->findUser($username);

            if ($user->getUsername() === 'hubert') {
                self::assertFalse($user->isDisabled());
            }
            else {
                self::assertTrue($user->isDisabled());
            }
        }
    }
}
