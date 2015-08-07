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

class DeleteGroupCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'staff']);
        $this->assertNotNull($group);

        $command = new DeleteGroupCommand(['id' => $group->getId()]);
        $this->command_bus->handle($command);

        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'staff']);
        $this->assertNull($group);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testNotFound()
    {
        $command = new DeleteGroupCommand(['id' => $this->getMaxId()]);
        $this->command_bus->handle($command);
    }
}
