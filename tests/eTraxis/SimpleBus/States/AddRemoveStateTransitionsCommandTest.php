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

use eTraxis\Dictionary\SystemRole;
use eTraxis\Entity\Group;
use eTraxis\Entity\State;
use eTraxis\Entity\StateGroupTransition;
use eTraxis\Entity\StateRoleTransition;
use eTraxis\Tests\BaseTestCase;

class AddRemoveStateTransitionsCommandTest extends BaseTestCase
{
    public function testAddGroupTransitions()
    {
        /** @var State $state_new */
        $state_new = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);
        self::assertNotNull($state_new);

        /** @var State $state_delivered */
        $state_delivered = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);
        self::assertNotNull($state_delivered);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Crew']);
        self::assertNotNull($group);

        /** @var StateGroupTransition $transition */
        $transition = $this->doctrine->getRepository(StateGroupTransition::class)->findOneBy([
            'fromState' => $state_new,
            'toState'   => $state_delivered,
            'group'     => $group,
        ]);
        self::assertNull($transition);

        $command = new AddStateTransitionsCommand([
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
        self::assertNotNull($state_new);

        /** @var State $state_delivered */
        $state_delivered = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);
        self::assertNotNull($state_delivered);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);
        self::assertNotNull($group);

        /** @var StateGroupTransition $transition */
        $transition = $this->doctrine->getRepository(StateGroupTransition::class)->findOneBy([
            'fromState' => $state_new,
            'toState'   => $state_delivered,
            'group'     => $group,
        ]);
        self::assertNotNull($transition);

        $command = new RemoveStateTransitionsCommand([
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
        self::assertNull($transition);
    }

    public function testAddRoleTransitions()
    {
        /** @var State $state_new */
        $state_new = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);
        self::assertNotNull($state_new);

        /** @var State $state_delivered */
        $state_delivered = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);
        self::assertNotNull($state_delivered);

        /** @var StateRoleTransition $transition */
        $transition = $this->doctrine->getRepository(StateRoleTransition::class)->findOneBy([
            'fromState' => $state_new,
            'toState'   => $state_delivered,
            'role'      => SystemRole::AUTHOR,
        ]);
        self::assertNull($transition);

        $command = new AddStateTransitionsCommand([
            'id'          => $state_new->getId(),
            'group'       => SystemRole::AUTHOR,
            'transitions' => [$state_delivered->getId()],
        ]);

        $this->command_bus->handle($command);

        $transition = $this->doctrine->getRepository(StateRoleTransition::class)->findOneBy([
            'fromState' => $state_new,
            'toState'   => $state_delivered,
            'role'      => SystemRole::AUTHOR,
        ]);
        self::assertNotNull($transition);
    }

    public function testRemoveRoleTransitions()
    {
        /** @var State $state_new */
        $state_new = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);
        self::assertNotNull($state_new);

        /** @var State $state_delivered */
        $state_delivered = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);
        self::assertNotNull($state_delivered);

        /** @var StateRoleTransition $transition */
        $transition = $this->doctrine->getRepository(StateRoleTransition::class)->findOneBy([
            'fromState' => $state_new,
            'toState'   => $state_delivered,
            'role'      => SystemRole::RESPONSIBLE,
        ]);
        self::assertNotNull($transition);

        $command = new RemoveStateTransitionsCommand([
            'id'          => $state_new->getId(),
            'group'       => SystemRole::RESPONSIBLE,
            'transitions' => [$state_delivered->getId()],
        ]);

        $this->command_bus->handle($command);

        $transition = $this->doctrine->getRepository(StateRoleTransition::class)->findOneBy([
            'fromState' => $state_new,
            'toState'   => $state_delivered,
            'role'      => SystemRole::RESPONSIBLE,
        ]);
        self::assertNull($transition);
    }

    public function testEmptyTransitions()
    {
        $total = count($this->doctrine->getRepository(StateRoleTransition::class)->findAll());

        /** @var State $state_new */
        $state_new = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);
        self::assertNotNull($state_new);

        /** @var State $state_produced */
        $state_produced = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Produced']);
        self::assertNotNull($state_produced);

        /** @var State $state_released */
        $state_released = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Released']);
        self::assertNotNull($state_released);

        $command = new AddStateTransitionsCommand([
            'id'          => $state_new->getId(),
            'group'       => SystemRole::RESPONSIBLE,
            'transitions' => [$state_produced->getId(), $state_released->getId()],
        ]);

        $this->command_bus->handle($command);

        self::assertCount($total, $this->doctrine->getRepository(StateRoleTransition::class)->findAll());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testNotFoundState()
    {
        /** @var State $state_delivered */
        $state_delivered = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);
        self::assertNotNull($state_delivered);

        $command = new AddStateTransitionsCommand([
            'id'          => $this->getMaxId(),
            'group'       => SystemRole::RESPONSIBLE,
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
        self::assertNotNull($state_new);

        /** @var State $state_delivered */
        $state_delivered = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);
        self::assertNotNull($state_delivered);

        $command = new AddStateTransitionsCommand([
            'id'          => $state_new->getId(),
            'group'       => $this->getMaxId(),
            'transitions' => [$state_delivered->getId()],
        ]);

        $this->command_bus->handle($command);
    }
}
