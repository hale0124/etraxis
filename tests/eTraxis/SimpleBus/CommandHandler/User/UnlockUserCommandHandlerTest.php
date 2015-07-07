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

use eTraxis\SimpleBus\Command\User\UnlockUserCommand;
use eTraxis\Tests\BaseTestCase;

class UnlockUserCommandHandlerTest extends BaseTestCase
{
    public function testUnlockUser()
    {
        $username = 'artem';

        /** @var \eTraxis\Model\User $user */
        $user = $this->doctrine->getRepository('eTraxis:User')->findOneBy([
            'username' => $username . '@eTraxis',
            'isLdap'   => false,
        ]);

        $this->assertNotNull($user);

        $user->setAuthAttempts(1);

        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();

        $command = new UnlockUserCommand([
            'username' => $username,
        ]);

        $this->command_bus->handle($command);

        $user = $this->doctrine->getRepository('eTraxis:User')->findOneBy([
            'username' => $username . '@eTraxis',
            'isLdap'   => false,
        ]);

        $this->assertEquals(0, $user->getAuthAttempts());
    }
}
