<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Groups;

use eTraxis\Tests\BaseTestCase;

class FindGroupCommandTest extends BaseTestCase
{
    public function testFound()
    {
        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'Staff']);

        $command = new FindGroupCommand([
            'id' => $group->getId(),
        ]);

        $result = $this->command_bus->handle($command);

        $this->assertEquals($group->getId(), $result->getId());
    }

    public function testNotFound()
    {
        $command = new FindGroupCommand([
            'id' => $this->getMaxId(),
        ]);

        $this->assertNull($this->command_bus->handle($command));
    }
}
