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

class CreateStateCommandTest extends BaseTestCase
{
    /**
     * @return  \eTraxis\Entity\Template
     */
    private function getTemplate()
    {
        return $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);
    }

    public function testSuccess()
    {
        /** @var \eTraxis\Entity\State $nextState */
        $template     = $this->getTemplate();
        $name         = 'Started';
        $abbreviation = 'S';
        $type         = State::TYPE_TRANSIENT;
        $responsible  = State::RESPONSIBLE_KEEP;
        $nextState    = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);

        /** @var \eTraxis\Entity\State $state */
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => $name]);

        $this->assertNull($state);

        $command = new CreateStateCommand([
            'template'     => $template->getId(),
            'name'         => $name,
            'abbreviation' => $abbreviation,
            'type'         => $type,
            'responsible'  => $responsible,
            'nextState'    => $nextState->getId(),
        ]);

        $this->command_bus->handle($command);

        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => $name]);

        $this->assertInstanceOf('eTraxis\Entity\State', $state);
        $this->assertEquals($template->getId(), $state->getTemplate()->getId());
        $this->assertEquals($name, $state->getName());
        $this->assertEquals($abbreviation, $state->getAbbreviation());
        $this->assertEquals($type, $state->getType());
        $this->assertEquals($responsible, $state->getResponsible());
        $this->assertEquals($nextState->getId(), $state->getNextState()->getId());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown template.
     */
    public function testUnknownTemplate()
    {
        $command = new CreateStateCommand([
            'template'     => $this->getMaxId(),
            'name'         => 'Started',
            'abbreviation' => 'S',
            'type'         => State::TYPE_TRANSIENT,
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
        $command = new CreateStateCommand([
            'template'     => $this->getTemplate()->getId(),
            'name'         => 'Started',
            'abbreviation' => 'S',
            'type'         => State::TYPE_TRANSIENT,
            'responsible'  => State::RESPONSIBLE_KEEP,
            'nextState'    => $this->getMaxId(),
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\SimpleBus\CommandException
     * @expectedExceptionMessage State with entered name already exists.
     */
    public function testNameConflict()
    {
        $command = new CreateStateCommand([
            'template'     => $this->getTemplate()->getId(),
            'name'         => 'Delivered',
            'abbreviation' => 'S',
            'type'         => State::TYPE_TRANSIENT,
            'responsible'  => State::RESPONSIBLE_KEEP,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\SimpleBus\CommandException
     * @expectedExceptionMessage State with entered abbreviation already exists.
     */
    public function testAbbreviationConflict()
    {
        $command = new CreateStateCommand([
            'template'     => $this->getTemplate()->getId(),
            'name'         => 'Started',
            'abbreviation' => 'D',
            'type'         => State::TYPE_TRANSIENT,
            'responsible'  => State::RESPONSIBLE_KEEP,
        ]);

        $this->command_bus->handle($command);
    }
}
