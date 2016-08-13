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

class DisableUsersCommandTest extends TransactionalTestCase
{
    public function testDisableUser()
    {
        $this->loginAs('hubert');

        $usernames = ['hubert', 'zapp', 'kif'];

        $ids = [];

        foreach ($usernames as $username) {
            $user  = $this->findUser($username);
            $ids[] = $user->getId();

            self::assertFalse($user->isDisabled());
        }

        $command = new DisableUsersCommand(['ids' => $ids]);

        $this->commandbus->handle($command);

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
