<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields;

use AltrEgo\AltrEgo;
use eTraxis\Entity\Field;
use eTraxis\Entity\State;
use eTraxis\SimpleBus\Fields\Handler\BaseFieldCommandHandler;
use eTraxis\Tests\BaseTestCase;

class BaseFieldCommandTest extends BaseTestCase
{
    public function testCreateSuccess()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        self::assertNotNull($state);

        $command = new CreateFieldBaseCommand([
            'state'        => $state->getId(),
            'name'         => 'Priority',
            'description'  => 'Urgency level',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
        ]);

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        /** @var mixed $handler */
        $handler = AltrEgo::create(new BaseFieldCommandHandler($this->validator, $manager));

        /** @var Field $field */
        $field = $handler->getEntity($command);

        self::assertInstanceOf(Field::class, $field);
        self::assertEquals($state, $field->getState());
        self::assertEquals('Priority', $field->getName());
        self::assertEquals('Urgency level', $field->getDescription());
        self::assertEquals(5, $field->getIndexNumber());
        self::assertTrue($field->isRequired());
        self::assertFalse($field->hasGuestAccess());
        self::assertFalse($field->getShowInEmails());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testCreateNotFound()
    {
        $command = new CreateFieldBaseCommand([
            'state'        => PHP_INT_MAX,
            'name'         => 'Priority',
            'description'  => 'Urgency level',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
        ]);

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        /** @var mixed $handler */
        $handler = AltrEgo::create(new BaseFieldCommandHandler($this->validator, $manager));
        $handler->getEntity($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Field with entered name already exists.
     */
    public function testCreateConflict()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        self::assertNotNull($state);

        $command = new CreateFieldBaseCommand([
            'state'        => $state->getId(),
            'name'         => 'Crew',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
        ]);

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        /** @var mixed $handler */
        $handler = AltrEgo::create(new BaseFieldCommandHandler($this->validator, $manager));
        $handler->getEntity($command);
    }

    public function testUpdateSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        self::assertNotNull($field);

        $command = new UpdateFieldBaseCommand([
            'id'           => $field->getId(),
            'name'         => 'Team',
            'description'  => 'New description',
            'required'     => false,
            'guestAccess'  => true,
            'showInEmails' => true,
        ]);

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        /** @var mixed $handler */
        $handler = AltrEgo::create(new BaseFieldCommandHandler($this->validator, $manager));

        /** @var Field $entity */
        $entity = $handler->getEntity($command);

        self::assertInstanceOf(Field::class, $entity);
        self::assertEquals('Team', $entity->getName());
        self::assertEquals('New description', $entity->getDescription());
        self::assertFalse($entity->isRequired());
        self::assertTrue($entity->hasGuestAccess());
        self::assertTrue($entity->getShowInEmails());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown field.
     */
    public function testUpdateNotFound()
    {
        $command = new UpdateFieldBaseCommand([
            'id'           => PHP_INT_MAX,
            'name'         => 'Team',
            'description'  => 'New description',
            'required'     => false,
            'guestAccess'  => true,
            'showInEmails' => true,
        ]);

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        /** @var mixed $handler */
        $handler = AltrEgo::create(new BaseFieldCommandHandler($this->validator, $manager));
        $handler->getEntity($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Field with entered name already exists.
     */
    public function testUpdateConflict()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        self::assertNotNull($field);

        $command = new UpdateFieldBaseCommand([
            'id'           => $field->getId(),
            'name'         => 'Notes',
            'description'  => 'New description',
            'required'     => false,
            'guestAccess'  => true,
            'showInEmails' => true,
        ]);

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        /** @var mixed $handler */
        $handler = AltrEgo::create(new BaseFieldCommandHandler($this->validator, $manager));
        $handler->getEntity($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Unsupported command.
     */
    public function testUnsupportedCommand()
    {
        $command = null;

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        /** @var mixed $handler */
        $handler = AltrEgo::create(new BaseFieldCommandHandler($this->validator, $manager));
        $handler->getEntity($command);
    }
}
