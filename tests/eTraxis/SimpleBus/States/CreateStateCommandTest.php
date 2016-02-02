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
        $type         = State::TYPE_INTERIM;
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

    public function testInitial()
    {
        /** @var \eTraxis\Repository\StatesRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:State');

        /** @var \eTraxis\Entity\State $nextState */
        $template     = $this->getTemplate();
        $name         = 'Very first';
        $abbreviation = 'VF';

        /** @var \eTraxis\Entity\State $initial */
        $initial = $repository->findOneBy(['name' => 'New']);
        $this->assertEquals(State::TYPE_INITIAL, $initial->getType());

        /** @var \eTraxis\Entity\State $state */
        $state = $repository->findOneBy(['name' => $name]);

        $this->assertNull($state);

        $command = new CreateStateCommand([
            'template'     => $template->getId(),
            'name'         => $name,
            'abbreviation' => $abbreviation,
            'type'         => State::TYPE_INITIAL,
            'responsible'  => State::RESPONSIBLE_KEEP,
        ]);

        $this->command_bus->handle($command);

        $state = $repository->findOneBy(['name' => $name]);

        $this->assertInstanceOf('eTraxis\Entity\State', $state);
        $this->assertEquals($template->getId(), $state->getTemplate()->getId());
        $this->assertEquals($name, $state->getName());
        $this->assertEquals($abbreviation, $state->getAbbreviation());
        $this->assertEquals(State::TYPE_INITIAL, $state->getType());

        $query = $repository->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.templateId = :id')
            ->andWhere('s.type = :initial')
            ->setParameter('id', $template->getId())
            ->setParameter('initial', State::TYPE_INITIAL)
        ;

        $count = $query->getQuery()->getSingleScalarResult();
        $this->assertEquals(1, $count);
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
            'type'         => State::TYPE_INTERIM,
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
            'type'         => State::TYPE_INTERIM,
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
        $command = new CreateStateCommand([
            'template'     => $this->getTemplate()->getId(),
            'name'         => 'Delivered',
            'abbreviation' => 'S',
            'type'         => State::TYPE_INTERIM,
            'responsible'  => State::RESPONSIBLE_KEEP,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage State with entered abbreviation already exists.
     */
    public function testAbbreviationConflict()
    {
        $command = new CreateStateCommand([
            'template'     => $this->getTemplate()->getId(),
            'name'         => 'Started',
            'abbreviation' => 'D',
            'type'         => State::TYPE_INTERIM,
            'responsible'  => State::RESPONSIBLE_KEEP,
        ]);

        $this->command_bus->handle($command);
    }
}
