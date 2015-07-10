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

class EnableUserCommandTest extends BaseTestCase
{
    public function testEnableUser()
    {
        $username = 'veins';

        /** @var \eTraxis\Model\User $user */
        $user = $this->doctrine->getRepository('eTraxis:User')->findOneBy([
            'username' => $username . '@eTraxis',
            'isLdap'   => false,
        ]);

        $this->assertNotNull($user);
        $this->assertTrue($user->isDisabled());

        $command = new EnableUserCommand([
            'id' => $user->getId(),
        ]);

        $this->command_bus->handle($command);

        $user = $this->doctrine->getRepository('eTraxis:User')->findOneBy([
            'username' => $username . '@eTraxis',
            'isLdap'   => false,
        ]);

        $this->assertFalse($user->isDisabled());
    }
}
