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

class GetGroupNonMembersCommandTest extends BaseTestCase
{
    public function testFound()
    {
        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'staff']);

        $command = new GetGroupNonMembersCommand([
            'id' => $group->getId(),
        ]);

        $result = $this->command_bus->handle($command);

        $users = array_map(function ($user) {
            /** @var \eTraxis\Entity\User $user */
            return $user->getUsername();
        }, $result);

        $expected = [
            'einstein',
            'artem',
            'veins',
            'francine',
            'hermes',
            'hubert',
            'kif',
            'zapp',
        ];

        $this->assertEquals($expected, $users);
    }

    public function testNotFound()
    {
        $command = new GetGroupNonMembersCommand([
            'id' => $this->getMaxId(),
        ]);

        $result = $this->command_bus->handle($command);

        $expected = [];

        $this->assertEquals($expected, $result);
    }
}
