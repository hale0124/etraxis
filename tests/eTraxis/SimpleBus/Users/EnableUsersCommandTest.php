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

class EnableUsersCommandTest extends BaseTestCase
{
    public function testEnableUser()
    {
        $user = $this->findUser('veins');

        $this->assertNotNull($user);
        $this->assertTrue($user->isDisabled());

        $command = new EnableUsersCommand([
            'ids' => [$user->getId()],
        ]);

        $this->command_bus->handle($command);

        $this->doctrine->getManager()->clear();

        $user = $this->findUser('veins');

        $this->assertFalse($user->isDisabled());
    }
}
