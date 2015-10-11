<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Users;

use eTraxis\Tests\BaseTestCase;

class AddGroupsCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        $user = $this->findUser('hubert');

        $groups = $this->command_bus->handle(
            new GetUserGroupsCommand([
                'id' => $user->getId(),
            ])
        );

        $others = $this->command_bus->handle(
            new GetOtherGroupsCommand([
                'id' => $user->getId(),
            ])
        );

        $this->assertNotCount(0, $groups);
        $this->assertNotCount(0, $others);

        $expected = count($groups) + count($others);

        $command = new AddGroupsCommand([
            'id'     => $user->getId(),
            'groups' => array_map(function ($group) {
                /** @var \eTraxis\Entity\Group $group */
                return $group->getId();
            }, $others),
        ]);

        $this->command_bus->handle($command);

        $groups = $this->command_bus->handle(
            new GetUserGroupsCommand([
                'id' => $user->getId(),
            ])
        );

        $others = $this->command_bus->handle(
            new GetOtherGroupsCommand([
                'id' => $user->getId(),
            ])
        );

        $this->assertCount($expected, $groups);
        $this->assertCount(0, $others);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testNotFound()
    {
        $command = new AddGroupsCommand([
            'id'     => $this->getMaxId(),
            'groups' => [1, 2, 3],
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\CommandBus\ValidationException
     */
    public function testEmptyGroups()
    {
        $user = $this->findUser('hubert');

        $command = new AddGroupsCommand([
            'id'     => $user->getId(),
            'groups' => [],
        ]);

        $this->command_bus->handle($command);
    }
}
