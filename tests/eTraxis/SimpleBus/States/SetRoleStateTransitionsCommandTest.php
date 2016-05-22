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
use eTraxis\Entity\State;
use eTraxis\Entity\StateRoleTransition;
use eTraxis\Tests\TransactionalTestCase;

class SetRoleStateTransitionsCommandTest extends TransactionalTestCase
{
    public function testAddRoleTransitions()
    {
        /** @var State $state_new */
        $state_new = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        /** @var State $state_delivered */
        $state_delivered = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        /** @var StateRoleTransition $transition */
        $transition = $this->doctrine->getRepository(StateRoleTransition::class)->findOneBy([
            'fromState' => $state_new,
            'toState'   => $state_delivered,
            'role'      => SystemRole::AUTHOR,
        ]);
        self::assertNull($transition);

        $command = new SetRoleStateTransitionsCommand([
            'id'          => $state_new->getId(),
            'role'        => SystemRole::AUTHOR,
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

        /** @var State $state_delivered */
        $state_delivered = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        /** @var StateRoleTransition $transition */
        $transition = $this->doctrine->getRepository(StateRoleTransition::class)->findOneBy([
            'fromState' => $state_new,
            'toState'   => $state_delivered,
            'role'      => SystemRole::RESPONSIBLE,
        ]);
        self::assertNotNull($transition);

        $command = new SetRoleStateTransitionsCommand([
            'id'          => $state_new->getId(),
            'role'        => SystemRole::RESPONSIBLE,
            'transitions' => [],
        ]);

        $this->command_bus->handle($command);

        $transition = $this->doctrine->getRepository(StateRoleTransition::class)->findOneBy([
            'fromState' => $state_new,
            'toState'   => $state_delivered,
            'role'      => SystemRole::RESPONSIBLE,
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

        $command = new SetRoleStateTransitionsCommand([
            'id'          => self::UNKNOWN_ENTITY_ID,
            'role'        => SystemRole::RESPONSIBLE,
            'transitions' => [$state_delivered->getId()],
        ]);

        $this->command_bus->handle($command);
    }
}
