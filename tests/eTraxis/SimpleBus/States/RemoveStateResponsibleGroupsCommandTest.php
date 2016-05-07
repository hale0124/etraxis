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
use eTraxis\Entity\StateResponsibleGroup;
use eTraxis\Tests\BaseTestCase;

class RemoveStateResponsibleGroupsCommandTest extends BaseTestCase
{
    public function testRemoveResponsibleGroups()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);
        self::assertNotNull($state);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Crew']);
        self::assertNotNull($group);

        $responsible = $this->doctrine->getRepository(StateResponsibleGroup::class)->findOneBy([
            'state' => $state,
            'group' => $group,
        ]);
        self::assertNotNull($responsible);

        $command = new RemoveStateResponsibleGroupsCommand([
            'id'     => $state->getId(),
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);

        $responsible = $this->doctrine->getRepository(StateResponsibleGroup::class)->findOneBy([
            'state' => $state,
            'group' => $group,
        ]);
        self::assertNull($responsible);
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

        $command = new RemoveStateResponsibleGroupsCommand([
            'id'     => self::UNKNOWN_ENTITY_ID,
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);
    }
}
