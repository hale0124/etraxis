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
use eTraxis\Tests\BaseTestCase;

class UpdateGroupCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Staff']);

        self::assertNotNull($group);
        self::assertNotEmpty($group->getDescription());

        $command = new UpdateGroupCommand([
            'id'          => $group->getId(),
            'name'        => 'Robots',
            'description' => 'Mechanical beings',
        ]);

        $this->command_bus->handle($command);

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
            'id'          => $this->getMaxId(),
            'name'        => 'Robots',
            'description' => 'Mechanical beings',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Group with entered name already exists.
     */
    public function testNameConflict()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Staff']);

        self::assertNotNull($group);

        $command = new UpdateGroupCommand([
            'id'   => $group->getId(),
            'name' => 'Crew',
        ]);

        $this->command_bus->handle($command);
    }
}
