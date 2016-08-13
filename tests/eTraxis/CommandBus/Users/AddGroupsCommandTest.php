<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Users;

use eTraxis\Entity\Group;
use eTraxis\Tests\TransactionalTestCase;

class AddGroupsCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        $user = $this->findUser('hubert');

        $groups = $user->getGroups();
        $others = $user->getOtherGroups();

        self::assertNotCount(0, $groups);
        self::assertNotCount(0, $others);

        $expected = count($groups) + count($others);

        $command = new AddGroupsCommand([
            'id'     => $user->getId(),
            'groups' => array_map(function (Group $group) {
                return $group->getId();
            }, $others),
        ]);

        $this->commandbus->handle($command);
        $this->doctrine->getManager()->refresh($user);

        $groups = $user->getGroups();
        $others = $user->getOtherGroups();

        self::assertCount($expected, $groups);
        self::assertCount(0, $others);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown user.
     */
    public function testNotFound()
    {
        $command = new AddGroupsCommand([
            'id'     => self::UNKNOWN_ENTITY_ID,
            'groups' => [1, 2, 3],
        ]);

        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \eTraxis\CommandBus\Middleware\ValidationException
     * @expectedExceptionMessage This collection should contain 1 element or more.
     */
    public function testEmptyGroups()
    {
        $user = $this->findUser('hubert');

        $command = new AddGroupsCommand([
            'id'     => $user->getId(),
            'groups' => [],
        ]);

        $this->commandbus->handle($command);
    }
}
