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

use eTraxis\Tests\BaseTestCase;

class DeleteProjectCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        $this->loginAs('hubert');

        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'eTraxis 1.0']);
        $this->assertNotNull($project);

        $command = new DeleteProjectCommand(['id' => $project->getId()]);
        $this->command_bus->handle($command);

        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'eTraxis 1.0']);
        $this->assertNull($project);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testAccessDenied()
    {
        $this->loginAs('hubert');

        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);
        $this->assertNotNull($project);

        $command = new DeleteProjectCommand(['id' => $project->getId()]);
        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown project.
     */
    public function testNotFound()
    {
        $this->loginAs('hubert');

        $command = new DeleteProjectCommand(['id' => $this->getMaxId()]);
        $this->command_bus->handle($command);
    }
}
