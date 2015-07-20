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

class DisableUsersCommandTest extends BaseTestCase
{
    public function testDisableUser()
    {
        $user = $this->findUser('artem');

        $this->assertNotNull($user);
        $this->assertFalse($user->isDisabled());

        $command = new DisableUsersCommand([
            'ids' => [$user->getId()],
        ]);

        $this->command_bus->handle($command);

        $this->doctrine->getManager()->clear();

        $user = $this->findUser('artem');

        $this->assertTrue($user->isDisabled());
    }
}
