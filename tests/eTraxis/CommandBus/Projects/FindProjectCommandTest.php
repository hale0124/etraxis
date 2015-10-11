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

class FindProjectCommandTest extends BaseTestCase
{
    public function testFound()
    {
        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);

        $command = new FindProjectCommand([
            'id' => $project->getId(),
        ]);

        $result = $this->command_bus->handle($command);

        $this->assertEquals($project->getId(), $result->getId());
    }

    public function testNotFound()
    {
        $command = new FindProjectCommand([
            'id' => $this->getMaxId(),
        ]);

        $this->assertNull($this->command_bus->handle($command));
    }
}
