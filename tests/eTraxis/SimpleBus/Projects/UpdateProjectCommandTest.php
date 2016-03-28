<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Projects;

use eTraxis\Entity\Project;
use eTraxis\Tests\BaseTestCase;

class UpdateProjectCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);

        self::assertNotNull($project);
        self::assertNotEmpty($project->getDescription());

        $command = new UpdateProjectCommand([
            'id'          => $project->getId(),
            'name'        => 'Awesome Express',
            'description' => 'Newspaper-delivery company',
            'suspended'   => true,
        ]);

        $this->command_bus->handle($command);

        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->find($project->getId());

        self::assertEquals('Awesome Express', $project->getName());
        self::assertEquals('Newspaper-delivery company', $project->getDescription());
        self::assertTrue($project->isSuspended());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown project.
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
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Project with entered name already exists.
     */
    public function testNameConflict()
    {
        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);

        self::assertNotNull($project);

        $command = new UpdateProjectCommand([
            'id'        => $project->getId(),
            'name'      => 'eTraxis 3.0',
            'suspended' => true,
        ]);

        $this->command_bus->handle($command);
    }
}
