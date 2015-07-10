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

class DisableUserCommandTest extends BaseTestCase
{
    public function testDisableUser()
    {
        $username = 'artem';

        /** @var \eTraxis\Model\User $user */
        $user = $this->doctrine->getRepository('eTraxis:User')->findOneBy([
            'username' => $username . '@eTraxis',
            'isLdap'   => false,
        ]);

        $this->assertNotNull($user);
        $this->assertFalse($user->isDisabled());

        $command = new DisableUserCommand([
            'id' => $user->getId(),
        ]);

        $this->command_bus->handle($command);

        $user = $this->doctrine->getRepository('eTraxis:User')->findOneBy([
            'username' => $username . '@eTraxis',
            'isLdap'   => false,
        ]);

        $this->assertTrue($user->isDisabled());
    }
}
