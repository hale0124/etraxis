<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Groups;

use eTraxis\Tests\BaseTestCase;

class AddUsersCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'Staff']);

        $members = $this->command_bus->handle(
            new GetGroupMembersCommand([
                'id' => $group->getId(),
            ])
        );

        $others = $this->command_bus->handle(
            new GetGroupNonMembersCommand([
                'id' => $group->getId(),
            ])
        );

        $this->assertNotCount(0, $members);
        $this->assertNotCount(0, $others);

        $expected = count($members) + count($others);

        $command = new AddUsersCommand([
            'id'    => $group->getId(),
            'users' => array_map(function ($user) {
                /** @var \eTraxis\Entity\User $user */
                return $user->getId();
            }, $others),
        ]);

        $this->command_bus->handle($command);

        $members = $this->command_bus->handle(
            new GetGroupMembersCommand([
                'id' => $group->getId(),
            ])
        );

        $others = $this->command_bus->handle(
            new GetGroupNonMembersCommand([
                'id' => $group->getId(),
            ])
        );

        $this->assertCount($expected, $members);
        $this->assertCount(0, $others);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testNotFound()
    {
        $command = new AddUsersCommand([
            'id'    => $this->getMaxId(),
            'users' => [1, 2, 3],
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\CommandBus\ValidationException
     */
    public function testEmptyGroups()
    {
        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'Staff']);

        $command = new AddUsersCommand([
            'id'    => $group->getId(),
            'users' => [],
        ]);

        $this->command_bus->handle($command);
    }
}
