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

class UnlockUserCommandTest extends TransactionalTestCase
{
    public function testUnlockUser()
    {
        $user = $this->findUser('artem');

        $auth_attempts = $this->client->getContainer()->getParameter('security_auth_attempts');
        $lock_time     = $this->client->getContainer()->getParameter('security_lock_time');

        do {} while(!$user->lock($auth_attempts, $lock_time));

        self::assertTrue($user->isLocked());

        $command = new UnlockUserCommand(['id' => $user->getId()]);
        $this->commandbus->handle($command);

        self::assertFalse($user->isLocked());
    }
}
