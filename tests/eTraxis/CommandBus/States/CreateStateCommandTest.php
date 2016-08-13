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
use eTraxis\Dictionary\StateType;
use eTraxis\Entity\State;
use eTraxis\Entity\Template;
use eTraxis\Tests\TransactionalTestCase;

class CreateStateCommandTest extends TransactionalTestCase
{
    /**
     * @return  \eTraxis\Entity\Template
     */
    private function getTemplate()
    {
        return $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);
    }

    public function testSuccess()
    {
        /** @var State $nextState */
        $template     = $this->getTemplate();
        $name         = 'Started';
        $abbreviation = 'S';
        $type         = StateType::IS_INTERIM;
        $responsible  = StateResponsible::KEEP;
        $nextState    = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => $name]);

        self::assertNull($state);

        $command = new CreateStateCommand([
            'template'     => $template->getId(),
            'name'         => $name,
            'abbreviation' => $abbreviation,
            'type'         => $type,
            'responsible'  => $responsible,
            'nextState'    => $nextState->getId(),
        ]);

        $this->commandbus->handle($command);

        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => $name]);

        self::assertInstanceOf(State::class, $state);
        self::assertEquals($template->getId(), $state->getTemplate()->getId());
        self::assertEquals($name, $state->getName());
        self::assertEquals($abbreviation, $state->getAbbreviation());
        self::assertEquals($type, $state->getType());
        self::assertEquals($responsible, $state->getResponsible());
        self::assertEquals($nextState->getId(), $state->getNextState()->getId());
    }

    public function testInitial()
    {
        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository(State::class);

        /** @var State $nextState */
        $template     = $this->getTemplate();
        $name         = 'Very first';
        $abbreviation = 'VF';

        /** @var State $initial */
        $initial = $repository->findOneBy(['name' => 'New']);
        self::assertEquals(StateType::IS_INITIAL, $initial->getType());

        /** @var State $state */
        $state = $repository->findOneBy(['name' => $name]);

        self::assertNull($state);

        $command = new CreateStateCommand([
            'template'     => $template->getId(),
            'name'         => $name,
            'abbreviation' => $abbreviation,
            'type'         => StateType::IS_INITIAL,
            'responsible'  => StateResponsible::KEEP,
        ]);

        $this->commandbus->handle($command);

        $state = $repository->findOneBy(['name' => $name]);

        self::assertInstanceOf(State::class, $state);
        self::assertEquals($template->getId(), $state->getTemplate()->getId());
        self::assertEquals($name, $state->getName());
        self::assertEquals($abbreviation, $state->getAbbreviation());
        self::assertEquals(StateType::IS_INITIAL, $state->getType());

        $query = $repository->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.template = :template')
            ->andWhere('s.type = :initial')
            ->setParameter('template', $template)
            ->setParameter('initial', StateType::IS_INITIAL)
        ;

        $count = $query->getQuery()->getSingleScalarResult();
        self::assertEquals(1, $count);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown template.
     */
    public function testUnknownTemplate()
    {
        $command = new CreateStateCommand([
            'template'     => self::UNKNOWN_ENTITY_ID,
            'name'         => 'Started',
            'abbreviation' => 'S',
            'type'         => StateType::IS_INTERIM,
            'responsible'  => StateResponsible::KEEP,
        ]);

        $this->commandbus->handle($command);
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
            'type'         => StateType::IS_INTERIM,
            'responsible'  => StateResponsible::KEEP,
            'nextState'    => self::UNKNOWN_ENTITY_ID,
        ]);

        $this->commandbus->handle($command);
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
            'type'         => StateType::IS_INTERIM,
            'responsible'  => StateResponsible::KEEP,
        ]);

        $this->commandbus->handle($command);
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
            'type'         => StateType::IS_INTERIM,
            'responsible'  => StateResponsible::KEEP,
        ]);

        $this->commandbus->handle($command);
    }
}
