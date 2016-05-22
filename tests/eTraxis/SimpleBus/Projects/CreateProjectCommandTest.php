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
use eTraxis\Tests\TransactionalTestCase;

class CreateProjectCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        $name        = 'Awesome Express';
        $description = 'Newspaper-delivery company';

        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => $name]);

        self::assertNull($project);

        $command = new CreateProjectCommand([
            'name'        => $name,
            'description' => $description,
            'suspended'   => true,
        ]);

        $this->command_bus->handle($command);

        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => $name]);

        self::assertInstanceOf(Project::class, $project);
        self::assertEquals($name, $project->getName());
        self::assertEquals($description, $project->getDescription());
        self::assertTrue($project->isSuspended());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Project with entered name already exists.
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
