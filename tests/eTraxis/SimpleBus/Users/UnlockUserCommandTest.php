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

class UnlockUserCommandTest extends BaseTestCase
{
    public function testUnlockUser()
    {
        $user = $this->findUser('artem');
        self::assertNotNull($user);

        $auth_attempts = $this->client->getContainer()->getParameter('security_auth_attempts');
        $lock_time     = $this->client->getContainer()->getParameter('security_lock_time');

        do {} while(!$user->lock($auth_attempts, $lock_time));

        self::assertFalse($user->isAccountNonLocked());

        $command = new UnlockUserCommand(['id' => $user->getId()]);
        $this->command_bus->handle($command);

        self::assertTrue($user->isAccountNonLocked());
    }
}
