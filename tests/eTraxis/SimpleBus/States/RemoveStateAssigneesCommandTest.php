<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\States;

use eTraxis\Entity\Group;
use eTraxis\Entity\State;
use eTraxis\Entity\StateAssignee;
use eTraxis\Tests\BaseTestCase;

class RemoveStateAssigneesCommandTest extends BaseTestCase
{
    public function testRemoveAssignees()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);
        self::assertNotNull($state);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Crew']);
        self::assertNotNull($group);

        /** @var StateAssignee $assignee */
        $assignee = $this->doctrine->getRepository(StateAssignee::class)->findOneBy([
            'state' => $state,
            'group' => $group,
        ]);
        self::assertNotNull($assignee);

        $command = new RemoveStateAssigneesCommand([
            'id'     => $state->getId(),
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);

        $assignee = $this->doctrine->getRepository(StateAssignee::class)->findOneBy([
            'state' => $state,
            'group' => $group,
        ]);
        self::assertNull($assignee);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testNotFoundState()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);
        self::assertNotNull($group);

        $command = new RemoveStateAssigneesCommand([
            'id'     => PHP_INT_MAX,
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);
    }
}
