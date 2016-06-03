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
use eTraxis\Tests\TransactionalTestCase;

class RemoveStateResponsibleGroupsCommandTest extends TransactionalTestCase
{
    public function testRemoveResponsibleGroups()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Crew']);

        self::assertArraysByValues([$group], $state->getResponsibleGroups());

        $command = new RemoveStateResponsibleGroupsCommand([
            'id'     => $state->getId(),
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);

        self::assertEmpty($state->getResponsibleGroups());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testNotFoundState()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);

        $command = new RemoveStateResponsibleGroupsCommand([
            'id'     => self::UNKNOWN_ENTITY_ID,
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);
    }
}
