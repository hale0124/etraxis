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
        $user = $this->findUser('artem');
        $this->assertNotNull($user);

        $id = $user->getId();

        $user->setAuthAttempts(1);

        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();

        $user = $this->doctrine->getRepository('eTraxis:User')->find($id);
        $this->assertEquals(1, $user->getAuthAttempts());

        $command = new UnlockUserCommand(['id' => $id]);
        $this->command_bus->handle($command);

        $user = $this->doctrine->getRepository('eTraxis:User')->find($id);
        $this->assertEquals(0, $user->getAuthAttempts());
    }
}
