<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Groups;

use eTraxis\Entity\Group;
use eTraxis\Entity\User;
use eTraxis\Tests\TransactionalTestCase;

class AddUsersCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var \eTraxis\Repository\GroupsRepository $repository */
        $repository = $this->doctrine->getRepository(Group::class);

        /** @var Group $group */
        $group = $repository->findOneBy(['name' => 'Staff']);

        $members = $group->getMembers();
        $others  = $group->getNonMembers();

        self::assertNotCount(0, $members);
        self::assertNotCount(0, $others);

        $expected = count($members) + count($others);

        $command = new AddUsersCommand([
            'id'    => $group->getId(),
            'users' => array_map(function (User $user) {
                return $user->getId();
            }, $others),
        ]);

        $this->commandbus->handle($command);

        $members = $group->getMembers();
        $others  = $group->getNonMembers();

        self::assertCount($expected, $members);
        self::assertCount(0, $others);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown group.
     */
    public function testNotFound()
    {
        $command = new AddUsersCommand([
            'id'    => self::UNKNOWN_ENTITY_ID,
            'users' => [1, 2, 3],
        ]);

        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \eTraxis\CommandBus\Middleware\ValidationException
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

        $this->commandbus->handle($command);
    }
}
