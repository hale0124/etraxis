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

use eTraxis\Entity\State;
use eTraxis\Tests\BaseTestCase;

class UpdateStateCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var State $nextState */
        $nextState = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        $this->assertNotNull($state);
        $this->assertEquals('Delivered', $state->getName());
        $this->assertEquals('D', $state->getAbbreviation());
        $this->assertEquals(State::RESPONSIBLE_REMOVE, $state->getResponsible());
        $this->assertNull($state->getNextState());

        $command = new UpdateStateCommand([
            'id'           => $state->getId(),
            'name'         => 'Completed',
            'abbreviation' => 'C',
            'responsible'  => State::RESPONSIBLE_KEEP,
            'nextState'    => $nextState->getId(),
        ]);

        $this->command_bus->handle($command);

        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        $this->assertNull($state);

        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Completed']);

        $this->assertNotNull($state);
        $this->assertEquals('Completed', $state->getName());
        $this->assertEquals('C', $state->getAbbreviation());
        $this->assertEquals(State::RESPONSIBLE_REMOVE, $state->getResponsible());
        $this->assertEquals($state->getNextState()->getId(), $state->getNextState()->getId());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testUnknownState()
    {
        $command = new UpdateStateCommand([
            'id'           => $this->getMaxId(),
            'name'         => 'Completed',
            'abbreviation' => 'C',
            'responsible'  => State::RESPONSIBLE_KEEP,
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
            'responsible'  => State::RESPONSIBLE_KEEP,
            'nextState'    => $this->getMaxId(),
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
            'responsible'  => State::RESPONSIBLE_REMOVE,
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
            'responsible'  => State::RESPONSIBLE_REMOVE,
        ]);

        $this->command_bus->handle($command);
    }
}
