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

use eTraxis\Dictionary\StateResponsible;
use eTraxis\Entity\State;
use eTraxis\Tests\TransactionalTestCase;

class UpdateStateCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var State $nextState */
        $nextState = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        self::assertEquals('Delivered', $state->getName());
        self::assertEquals('D', $state->getAbbreviation());
        self::assertEquals(StateResponsible::REMOVE, $state->getResponsible());
        self::assertNull($state->getNextState());

        $command = new UpdateStateCommand([
            'id'           => $state->getId(),
            'name'         => 'Completed',
            'abbreviation' => 'C',
            'responsible'  => StateResponsible::KEEP,
            'nextState'    => $nextState->getId(),
        ]);

        $this->command_bus->handle($command);

        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        self::assertNull($state);

        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Completed']);

        self::assertEquals('Completed', $state->getName());
        self::assertEquals('C', $state->getAbbreviation());
        self::assertEquals(StateResponsible::REMOVE, $state->getResponsible());
        self::assertNull($state->getNextState());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testUnknownState()
    {
        $command = new UpdateStateCommand([
            'id'           => self::UNKNOWN_ENTITY_ID,
            'name'         => 'Completed',
            'abbreviation' => 'C',
            'responsible'  => StateResponsible::KEEP,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown next state.
     */
    public function testUnknownNextState()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        $command = new UpdateStateCommand([
            'id'           => $state->getId(),
            'name'         => 'Completed',
            'abbreviation' => 'C',
            'responsible'  => StateResponsible::KEEP,
            'nextState'    => self::UNKNOWN_ENTITY_ID,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage State with entered name already exists.
     */
    public function testNameConflict()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        $command = new UpdateStateCommand([
            'id'           => $state->getId(),
            'name'         => 'New',
            'abbreviation' => 'D',
            'responsible'  => StateResponsible::REMOVE,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage State with entered abbreviation already exists.
     */
    public function testAbbreviationConflict()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        $command = new UpdateStateCommand([
            'id'           => $state->getId(),
            'name'         => 'Delivered',
            'abbreviation' => 'N',
            'responsible'  => StateResponsible::REMOVE,
        ]);

        $this->command_bus->handle($command);
    }
}
