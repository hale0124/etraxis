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
use eTraxis\Entity\Project;
use eTraxis\Tests\BaseTestCase;

class CreateGroupCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        $name        = 'Robots';
        $description = 'Mechanical beings';

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => $name]);

        self::assertNull($group);

        $command = new CreateGroupCommand([
            'name'        => $name,
            'description' => $description,
        ]);

        $this->command_bus->handle($command);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => $name]);

        self::assertInstanceOf(Group::class, $group);
        self::assertEquals($name, $group->getName());
        self::assertEquals($description, $group->getDescription());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown project.
     */
    public function testUnknownProject()
    {
        $command = new CreateGroupCommand([
            'name'    => 'Robots',
            'project' => $this->getMaxId(),
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Group with entered name already exists.
     */
    public function testGlobalGroupConflict()
    {
        $command = new CreateGroupCommand([
            'name' => 'Nimbus',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Group with entered name already exists.
     */
    public function testLocalGroupConflict()
    {
        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);

        $command = new CreateGroupCommand([
            'name'    => 'Staff',
            'project' => $project->getId(),
        ]);

        $this->command_bus->handle($command);
    }
}
