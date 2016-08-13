<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Projects;

use eTraxis\Entity\Project;
use eTraxis\Tests\TransactionalTestCase;

class DeleteProjectCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        $this->loginAs('hubert');

        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'eTraxis 1.0']);
        self::assertNotNull($project);

        $command = new DeleteProjectCommand(['id' => $project->getId()]);
        $this->commandbus->handle($command);

        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'eTraxis 1.0']);
        self::assertNull($project);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testAccessDenied()
    {
        $this->loginAs('hubert');

        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);

        $command = new DeleteProjectCommand(['id' => $project->getId()]);
        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown project.
     */
    public function testNotFound()
    {
        $this->loginAs('hubert');

        $command = new DeleteProjectCommand(['id' => self::UNKNOWN_ENTITY_ID]);
        $this->commandbus->handle($command);
    }
}
