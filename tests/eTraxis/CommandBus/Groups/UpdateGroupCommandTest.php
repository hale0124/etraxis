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
use eTraxis\Tests\TransactionalTestCase;

class UpdateGroupCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Staff']);

        self::assertNotEmpty($group->getDescription());

        $command = new UpdateGroupCommand([
            'id'          => $group->getId(),
            'name'        => 'Robots',
            'description' => 'Mechanical beings',
        ]);

        $this->commandbus->handle($command);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->find($group->getId());

        self::assertEquals('Robots', $group->getName());
        self::assertEquals('Mechanical beings', $group->getDescription());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown group.
     */
    public function testUnknownGroup()
    {
        $command = new UpdateGroupCommand([
            'id'          => self::UNKNOWN_ENTITY_ID,
            'name'        => 'Robots',
            'description' => 'Mechanical beings',
        ]);

        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Group with entered name already exists.
     */
    public function testNameConflict()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Staff']);

        $command = new UpdateGroupCommand([
            'id'   => $group->getId(),
            'name' => 'Crew',
        ]);

        $this->commandbus->handle($command);
    }
}
