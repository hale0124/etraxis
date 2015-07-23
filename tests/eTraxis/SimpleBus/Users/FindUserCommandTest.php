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

class FindUserCommandTest extends BaseTestCase
{
    public function testFound()
    {
        $user = $this->findUser('artem');

        $command = new FindUserCommand([
            'id' => $user->getId(),
        ]);

        $this->command_bus->handle($command);

        $this->assertEquals($user->getId(), $command->result->getId());
    }

    public function testNotFound()
    {
        $command = new FindUserCommand([
            'id' => $this->getMaxId(),
        ]);

        $this->command_bus->handle($command);

        $this->assertNull($command->result);
    }
}
