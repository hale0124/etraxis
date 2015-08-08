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

namespace eTraxis\CommandBus\Groups;

use eTraxis\Tests\BaseTestCase;

class GetGroupMembersCommandTest extends BaseTestCase
{
    public function testFound()
    {
        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'Staff']);

        $command = new GetGroupMembersCommand([
            'id' => $group->getId(),
        ]);

        $result = $this->command_bus->handle($command);

        $users = array_map(function ($user) {
            /** @var \eTraxis\Entity\User $user */
            return $user->getUsername();
        }, $result);

        $expected = [
            'bender',
            'amy',
            'zoidberg',
            'fry',
            'scruffy',
            'leela',
        ];

        $this->assertEquals($expected, $users);
    }

    public function testNotFound()
    {
        $command = new GetGroupMembersCommand([
            'id' => $this->getMaxId(),
        ]);

        $result = $this->command_bus->handle($command);

        $expected = [];

        $this->assertEquals($expected, $result);
    }
}
