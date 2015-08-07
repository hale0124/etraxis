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

class UpdateGroupCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'staff']);

        $this->assertNotNull($group);
        $this->assertNotEmpty($group->getDescription());

        $command = new UpdateGroupCommand([
            'id'          => $group->getId(),
            'name'        => 'Robots',
            'description' => 'Mechanical beings',
        ]);

        $this->command_bus->handle($command);

        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->find($group->getId());

        $this->assertEquals('Robots', $group->getName());
        $this->assertEquals('Mechanical beings', $group->getDescription());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
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
     * @expectedException \eTraxis\CommandBus\CommandException
     */
    public function testNameConflict()
    {
        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'staff']);

        $this->assertNotNull($group);

        $command = new UpdateGroupCommand([
            'id'   => $group->getId(),
            'name' => 'crew',
        ]);

        $this->command_bus->handle($command);
    }
}
