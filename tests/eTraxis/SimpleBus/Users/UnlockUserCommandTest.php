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

use eTraxis\Entity\User;
use eTraxis\Tests\BaseTestCase;

class UnlockUserCommandTest extends BaseTestCase
{
    public function testUnlockUser()
    {
        $user = $this->findUser('artem');
        self::assertNotNull($user);

        $id = $user->getId();

        $user->setAuthAttempts(1);

        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();

        $user = $this->doctrine->getRepository(User::class)->find($id);
        self::assertEquals(1, $user->getAuthAttempts());

        $command = new UnlockUserCommand(['id' => $id]);
        $this->command_bus->handle($command);

        $user = $this->doctrine->getRepository(User::class)->find($id);
        self::assertEquals(0, $user->getAuthAttempts());
    }
}
