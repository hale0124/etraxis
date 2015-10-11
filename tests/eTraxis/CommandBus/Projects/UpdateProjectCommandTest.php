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

class UpdateProjectCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);

        $this->assertNotNull($project);
        $this->assertNotEmpty($project->getDescription());

        $command = new UpdateProjectCommand([
            'id'          => $project->getId(),
            'name'        => 'Awesome Express',
            'description' => 'Newspaper-delivery company',
            'suspended'   => true,
        ]);

        $this->command_bus->handle($command);

        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->find($project->getId());

        $this->assertEquals('Awesome Express', $project->getName());
        $this->assertEquals('Newspaper-delivery company', $project->getDescription());
        $this->assertTrue($project->isSuspended());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testUnknownProject()
    {
        $command = new UpdateProjectCommand([
            'id'          => $this->getMaxId(),
            'name'        => 'Awesome Express',
            'description' => 'Newspaper-delivery company',
            'suspended'   => true,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\CommandBus\CommandException
     */
    public function testNameConflict()
    {
        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);

        $this->assertNotNull($project);

        $command = new UpdateProjectCommand([
            'id'        => $project->getId(),
            'name'      => 'eTraxis 3.0',
            'suspended' => true,
        ]);

        $this->command_bus->handle($command);
    }
}
