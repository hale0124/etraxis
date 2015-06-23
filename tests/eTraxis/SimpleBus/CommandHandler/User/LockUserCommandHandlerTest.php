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

namespace eTraxis\SimpleBus\CommandHandler\User;

use eTraxis\SimpleBus\Command\User\LockUserCommand;
use eTraxis\Tests\BaseTestCase;

class LockUserCommandHandlerTest extends BaseTestCase
{
    public function testLockUser()
    {
        $username = 'artem';

        /** @var \eTraxis\Model\User $user */
        $user = $this->doctrine->getRepository('eTraxis:User')->findOneBy([
            'username' => $username . '@eTraxis',
            'isLdap'   => false,
        ]);

        $this->assertNotNull($user);

        $expected = $user->getAuthAttempts() + 1;

        $command = new LockUserCommand();

        $command->username = $username;

        // first time
        $this->command_bus->handle($command);

        $user = $this->doctrine->getRepository('eTraxis:User')->findOneBy([
            'username' => $username . '@eTraxis',
            'isLdap'   => false,
        ]);

        $this->assertEquals($expected, $user->getAuthAttempts());

        // second time
        $this->command_bus->handle($command);

        $user = $this->doctrine->getRepository('eTraxis:User')->findOneBy([
            'username' => $username . '@eTraxis',
            'isLdap'   => false,
        ]);

        $this->assertFalse($user->isAccountNonLocked());
    }
}
