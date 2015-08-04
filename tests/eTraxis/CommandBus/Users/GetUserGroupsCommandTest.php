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

namespace eTraxis\CommandBus\Users;

use eTraxis\Tests\BaseTestCase;

class GetUserGroupsCommandTest extends BaseTestCase
{
    public function testFound()
    {
        $user = $this->findUser('hubert');

        $command = new GetUserGroupsCommand([
            'id' => $user->getId(),
        ]);

        $result = $this->command_bus->handle($command);

        $groups = array_map(function ($group) {
            /** @var \eTraxis\Entity\Group $group */
            return $group->getName();
        }, $result);

        $expected = [
            'Planet Express, Inc.',
            'Crew',
            'Managers',
        ];

        $this->assertEquals($expected, $groups);
    }

    public function testNotFound()
    {
        $command = new GetUserGroupsCommand([
            'id' => $this->getMaxId(),
        ]);

        $result = $this->command_bus->handle($command);

        $expected = [];

        $this->assertEquals($expected, $result);
    }
}
