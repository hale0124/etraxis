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

class CreateGroupCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        $name        = 'Robots';
        $description = 'Mechanical beings';

        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => $name]);

        $this->assertNull($group);

        $command = new CreateGroupCommand([
            'name'        => $name,
            'description' => $description,
        ]);

        $result = $this->command_bus->handle($command);

        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => $name]);

        $id = $group->getId();

        $this->assertInstanceOf('eTraxis\Entity\Group', $group);
        $this->assertEquals($id, $result);
        $this->assertEquals($name, $group->getName());
        $this->assertEquals($description, $group->getDescription());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
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
     * @expectedException \eTraxis\CommandBus\CommandException
     */
    public function testGlobalGroupConflict()
    {
        $command = new CreateGroupCommand([
            'name' => 'Nimbus',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\CommandBus\CommandException
     */
    public function testLocalGroupConflict()
    {
        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);

        $command = new CreateGroupCommand([
            'name'    => 'Staff',
            'project' => $project->getId(),
        ]);

        $this->command_bus->handle($command);
    }
}
