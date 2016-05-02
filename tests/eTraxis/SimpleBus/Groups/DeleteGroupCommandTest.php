<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Groups;

use eTraxis\Entity\Group;
use eTraxis\Tests\BaseTestCase;

class DeleteGroupCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Staff']);
        self::assertNotNull($group);

        $command = new DeleteGroupCommand(['id' => $group->getId()]);
        $this->command_bus->handle($command);

        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Staff']);
        self::assertNull($group);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown group.
     */
    public function testNotFound()
    {
        $command = new DeleteGroupCommand(['id' => PHP_INT_MAX]);
        $this->command_bus->handle($command);
    }
}
