<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Projects;

use eTraxis\Tests\BaseTestCase;

class CreateProjectCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        $name        = 'Awesome Express';
        $description = 'Newspaper-delivery company';

        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => $name]);

        $this->assertNull($project);

        $command = new CreateProjectCommand([
            'name'        => $name,
            'description' => $description,
            'suspended'   => true,
        ]);

        $result = $this->command_bus->handle($command);

        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => $name]);

        $id = $project->getId();

        $this->assertInstanceOf('eTraxis\Entity\Project', $project);
        $this->assertEquals($id, $result);
        $this->assertEquals($name, $project->getName());
        $this->assertEquals($description, $project->getDescription());
        $this->assertTrue($project->isSuspended());
    }

    /**
     * @expectedException \eTraxis\CommandBus\CommandException
     */
    public function testProjectConflict()
    {
        $command = new CreateProjectCommand([
            'name'      => 'Planet Express',
            'suspended' => false,
        ]);

        $this->command_bus->handle($command);
    }
}
