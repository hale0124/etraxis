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

use eTraxis\Collection\SystemRole;
use eTraxis\Tests\BaseTestCase;

class AddRemoveStateTransitionsCommandTest extends BaseTestCase
{
    public function testAddGroupTransitions()
    {
        /** @var \eTraxis\Entity\State $state_new */
        $state_new = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'New']);
        $this->assertNotNull($state_new);

        /** @var \eTraxis\Entity\State $state_delivered */
        $state_delivered = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);
        $this->assertNotNull($state_delivered);

        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'Crew']);
        $this->assertNotNull($group);

        /** @var \eTraxis\Entity\StateGroupTransition $transition */
        $transition = $this->doctrine->getRepository('eTraxis:StateGroupTransition')->findOneBy([
            'fromStateId' => $state_new->getId(),
            'toStateId'   => $state_delivered->getId(),
            'groupId'     => $group->getId(),
        ]);
        $this->assertNull($transition);

        $command = new AddStateTransitionsCommand([
            'id'          => $state_new->getId(),
            'group'       => $group->getId(),
            'transitions' => [$state_delivered->getId()],
        ]);

        $this->command_bus->handle($command);

        $transition = $this->doctrine->getRepository('eTraxis:StateGroupTransition')->findOneBy([
            'fromStateId' => $state_new->getId(),
            'toStateId'   => $state_delivered->getId(),
            'groupId'     => $group->getId(),
        ]);
        $this->assertNotNull($transition);
    }

    public function testRemoveGroupTransitions()
    {
        /** @var \eTraxis\Entity\State $state_new */
        $state_new = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'New']);
        $this->assertNotNull($state_new);

        /** @var \eTraxis\Entity\State $state_delivered */
        $state_delivered = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);
        $this->assertNotNull($state_delivered);

        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'Managers']);
        $this->assertNotNull($group);

        /** @var \eTraxis\Entity\StateGroupTransition $transition */
        $transition = $this->doctrine->getRepository('eTraxis:StateGroupTransition')->findOneBy([
            'fromStateId' => $state_new->getId(),
            'toStateId'   => $state_delivered->getId(),
            'groupId'     => $group->getId(),
        ]);
        $this->assertNotNull($transition);

        $command = new RemoveStateTransitionsCommand([
            'id'          => $state_new->getId(),
            'group'       => $group->getId(),
            'transitions' => [$state_delivered->getId()],
        ]);

        $this->command_bus->handle($command);

        $transition = $this->doctrine->getRepository('eTraxis:StateGroupTransition')->findOneBy([
            'fromStateId' => $state_new->getId(),
            'toStateId'   => $state_delivered->getId(),
            'groupId'     => $group->getId(),
        ]);
        $this->assertNull($transition);
    }

    public function testAddRoleTransitions()
    {
        /** @var \eTraxis\Entity\State $state_new */
        $state_new = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'New']);
        $this->assertNotNull($state_new);

        /** @var \eTraxis\Entity\State $state_delivered */
        $state_delivered = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);
        $this->assertNotNull($state_delivered);

        /** @var \eTraxis\Entity\StateRoleTransition $transition */
        $transition = $this->doctrine->getRepository('eTraxis:StateRoleTransition')->findOneBy([
            'fromStateId' => $state_new->getId(),
            'toStateId'   => $state_delivered->getId(),
            'role'        => SystemRole::AUTHOR,
        ]);
        $this->assertNull($transition);

        $command = new AddStateTransitionsCommand([
            'id'          => $state_new->getId(),
            'group'       => SystemRole::AUTHOR,
            'transitions' => [$state_delivered->getId()],
        ]);

        $this->command_bus->handle($command);

        $transition = $this->doctrine->getRepository('eTraxis:StateRoleTransition')->findOneBy([
            'fromStateId' => $state_new->getId(),
            'toStateId'   => $state_delivered->getId(),
            'role'        => SystemRole::AUTHOR,
        ]);
        $this->assertNotNull($transition);
    }

    public function testRemoveRoleTransitions()
    {
        /** @var \eTraxis\Entity\State $state_new */
        $state_new = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'New']);
        $this->assertNotNull($state_new);

        /** @var \eTraxis\Entity\State $state_delivered */
        $state_delivered = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);
        $this->assertNotNull($state_delivered);

        /** @var \eTraxis\Entity\StateRoleTransition $transition */
        $transition = $this->doctrine->getRepository('eTraxis:StateRoleTransition')->findOneBy([
            'fromStateId' => $state_new->getId(),
            'toStateId'   => $state_delivered->getId(),
            'role'        => SystemRole::RESPONSIBLE,
        ]);
        $this->assertNotNull($transition);

        $command = new RemoveStateTransitionsCommand([
            'id'          => $state_new->getId(),
            'group'       => SystemRole::RESPONSIBLE,
            'transitions' => [$state_delivered->getId()],
        ]);

        $this->command_bus->handle($command);

        $transition = $this->doctrine->getRepository('eTraxis:StateRoleTransition')->findOneBy([
            'fromStateId' => $state_new->getId(),
            'toStateId'   => $state_delivered->getId(),
            'role'        => SystemRole::RESPONSIBLE,
        ]);
        $this->assertNull($transition);
    }

    public function testEmptyTransitions()
    {
        $total = count($this->doctrine->getRepository('eTraxis:StateRoleTransition')->findAll());

        /** @var \eTraxis\Entity\State $state_new */
        $state_new = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'New']);
        $this->assertNotNull($state_new);

        /** @var \eTraxis\Entity\State $state_produced */
        $state_produced = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Produced']);
        $this->assertNotNull($state_produced);

        /** @var \eTraxis\Entity\State $state_released */
        $state_released = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Released']);
        $this->assertNotNull($state_released);

        $command = new AddStateTransitionsCommand([
            'id'          => $state_new->getId(),
            'group'       => SystemRole::RESPONSIBLE,
            'transitions' => [$state_produced->getId(), $state_released->getId()],
        ]);

        $this->command_bus->handle($command);

        $this->assertCount($total, $this->doctrine->getRepository('eTraxis:StateRoleTransition')->findAll());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testNotFoundState()
    {
        /** @var \eTraxis\Entity\State $state_delivered */
        $state_delivered = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);
        $this->assertNotNull($state_delivered);

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
        /** @var \eTraxis\Entity\State $state_new */
        $state_new = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'New']);
        $this->assertNotNull($state_new);

        /** @var \eTraxis\Entity\State $state_delivered */
        $state_delivered = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);
        $this->assertNotNull($state_delivered);

        $command = new AddStateTransitionsCommand([
            'id'          => $state_new->getId(),
            'group'       => $this->getMaxId(),
            'transitions' => [$state_delivered->getId()],
        ]);

        $this->command_bus->handle($command);
    }
}
