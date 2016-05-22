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
use eTraxis\Entity\StateGroupTransition;
use eTraxis\Tests\TransactionalTestCase;

class SetGroupStateTransitionsCommandTest extends TransactionalTestCase
{
    public function testAddGroupTransitions()
    {
        /** @var State $state_new */
        $state_new = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        /** @var State $state_delivered */
        $state_delivered = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Crew']);

        /** @var StateGroupTransition $transition */
        $transition = $this->doctrine->getRepository(StateGroupTransition::class)->findOneBy([
            'fromState' => $state_new,
            'toState'   => $state_delivered,
            'group'     => $group,
        ]);
        self::assertNull($transition);

        $command = new SetGroupStateTransitionsCommand([
            'id'          => $state_new->getId(),
            'group'       => $group->getId(),
            'transitions' => [$state_delivered->getId()],
        ]);

        $this->command_bus->handle($command);

        $transition = $this->doctrine->getRepository(StateGroupTransition::class)->findOneBy([
            'fromState' => $state_new,
            'toState'   => $state_delivered,
            'group'     => $group,
        ]);
        self::assertNotNull($transition);
    }

    public function testRemoveGroupTransitions()
    {
        /** @var State $state_new */
        $state_new = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        /** @var State $state_delivered */
        $state_delivered = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);

        /** @var StateGroupTransition $transition */
        $transition = $this->doctrine->getRepository(StateGroupTransition::class)->findOneBy([
            'fromState' => $state_new,
            'toState'   => $state_delivered,
            'group'     => $group,
        ]);
        self::assertNotNull($transition);

        $command = new SetGroupStateTransitionsCommand([
            'id'          => $state_new->getId(),
            'group'       => $group->getId(),
            'transitions' => [],
        ]);

        $this->command_bus->handle($command);

        $transition = $this->doctrine->getRepository(StateGroupTransition::class)->findOneBy([
            'fromState' => $state_new,
            'toState'   => $state_delivered,
            'group'     => $group,
        ]);
        self::assertNull($transition);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testNotFoundState()
    {
        /** @var State $state_delivered */
        $state_delivered = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Crew']);

        $command = new SetGroupStateTransitionsCommand([
            'id'          => self::UNKNOWN_ENTITY_ID,
            'group'       => $group->getId(),
            'transitions' => [$state_delivered->getId()],
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown group.
     */
    public function testNotFoundGroup()
    {
        /** @var State $state_new */
        $state_new = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        /** @var State $state_delivered */
        $state_delivered = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        $command = new SetGroupStateTransitionsCommand([
            'id'          => $state_new->getId(),
            'group'       => self::UNKNOWN_ENTITY_ID,
            'transitions' => [$state_delivered->getId()],
        ]);

        $this->command_bus->handle($command);
    }
}
