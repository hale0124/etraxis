<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\States;

use eTraxis\Entity\Group;
use eTraxis\Entity\State;
use eTraxis\Tests\TransactionalTestCase;

class SetGroupStateTransitionsCommandTest extends TransactionalTestCase
{
    /** @var State */
    private $draft;

    /** @var State */
    private $accepted;

    /** @var State */
    private $deprecated;

    protected function setUp()
    {
        parent::setUp();

        $repository = $this->doctrine->getRepository(State::class);

        $this->draft      = $repository->findOneBy(['name' => 'Draft']);
        $this->accepted   = $repository->findOneBy(['name' => 'Accepted']);
        $this->deprecated = $repository->findOneBy(['name' => 'Deprecated']);
    }

    public function testAddGroupTransitions()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Members']);

        self::assertArraysByValues([$this->accepted], $this->draft->getGroupTransitions($group));

        $command = new SetGroupStateTransitionsCommand([
            'id'          => $this->draft->getId(),
            'group'       => $group->getId(),
            'transitions' => [
                $this->accepted->getId(),
                $this->deprecated->getId(),
            ],
        ]);

        $this->command_bus->handle($command);

        self::assertArraysByValues([$this->accepted, $this->deprecated], $this->draft->getGroupTransitions($group));
    }

    public function testRemoveGroupTransitions()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Members']);

        self::assertArraysByValues([$this->accepted], $this->draft->getGroupTransitions($group));

        $command = new SetGroupStateTransitionsCommand([
            'id'          => $this->draft->getId(),
            'group'       => $group->getId(),
            'transitions' => [
                $this->deprecated->getId(),
            ],
        ]);

        $this->command_bus->handle($command);

        self::assertArraysByValues([$this->deprecated], $this->draft->getGroupTransitions($group));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testNotFoundState()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Members']);

        $command = new SetGroupStateTransitionsCommand([
            'id'          => self::UNKNOWN_ENTITY_ID,
            'group'       => $group->getId(),
            'transitions' => [
                $this->accepted->getId(),
            ],
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown group.
     */
    public function testNotFoundGroup()
    {
        $command = new SetGroupStateTransitionsCommand([
            'id'          => $this->draft->getId(),
            'group'       => self::UNKNOWN_ENTITY_ID,
            'transitions' => [
                $this->accepted->getId(),
            ],
        ]);

        $this->command_bus->handle($command);
    }
}
