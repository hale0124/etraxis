<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
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
        /** @var \eTraxis\Entity\State $nextState */
        $nextState = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'New']);

        /** @var \eTraxis\Entity\State $state */
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);

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

        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);

        $this->assertNull($state);

        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Completed']);

        $this->assertNotNull($state);
        $this->assertEquals('Completed', $state->getName());
        $this->assertEquals('C', $state->getAbbreviation());
        $this->assertEquals(State::RESPONSIBLE_KEEP, $state->getResponsible());
        $this->assertEquals($state->getNextState()->getId(), $state->getNextState()->getId());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
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
     */
    public function testUnknownNextState()
    {
        /** @var \eTraxis\Entity\State $state */
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);

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
     * @expectedException \eTraxis\SimpleBus\CommandException
     */
    public function testNameConflict()
    {
        /** @var \eTraxis\Entity\State $state */
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);

        $command = new UpdateStateCommand([
            'id'           => $state->getId(),
            'name'         => 'New',
            'abbreviation' => 'D',
            'responsible'  => State::RESPONSIBLE_REMOVE,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\SimpleBus\CommandException
     */
    public function testAbbreviationConflict()
    {
        /** @var \eTraxis\Entity\State $state */
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);

        $command = new UpdateStateCommand([
            'id'           => $state->getId(),
            'name'         => 'Delivered',
            'abbreviation' => 'N',
            'responsible'  => State::RESPONSIBLE_REMOVE,
        ]);

        $this->command_bus->handle($command);
    }
}
