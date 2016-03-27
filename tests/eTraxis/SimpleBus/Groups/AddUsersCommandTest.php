<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Groups;

use eTraxis\Entity\Group;
use eTraxis\Entity\User;
use eTraxis\Tests\BaseTestCase;

class AddUsersCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var \eTraxis\Repository\GroupsRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(Group::class);

        /** @var Group $group */
        $group = $repository->findOneBy(['name' => 'Staff']);

        $members = $repository->getGroupMembers($group->getId());
        $others  = $repository->getGroupNonMembers($group->getId());

        $this->assertNotCount(0, $members);
        $this->assertNotCount(0, $others);

        $expected = count($members) + count($others);

        $command = new AddUsersCommand([
            'id'    => $group->getId(),
            'users' => array_map(function (User $user) {
                return $user->getId();
            }, $others),
        ]);

        $this->command_bus->handle($command);

        $members = $repository->getGroupMembers($group->getId());
        $others  = $repository->getGroupNonMembers($group->getId());

        $this->assertCount($expected, $members);
        $this->assertCount(0, $others);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown group.
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
     * @expectedException \SimpleBus\ValidationException
     * @expectedExceptionMessage This collection should contain 1 element or more.
     */
    public function testEmptyGroups()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Staff']);

        $command = new AddUsersCommand([
            'id'    => $group->getId(),
            'users' => [],
        ]);

        $this->command_bus->handle($command);
    }
}
