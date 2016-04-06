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

use eTraxis\Entity\Field;
use eTraxis\Entity\State;
use eTraxis\Entity\Template;
use eTraxis\Tests\BaseTestCase;

class BaseFieldCommandTest extends BaseTestCase
{
    public function testCreateByTemplateSuccess()
    {
        /** @var Template $state */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        self::assertNotNull($template);

        $command = new CreateFieldBaseCommand([
            'template'     => $template->getId(),
            'name'         => 'Priority',
            'description'  => 'Urgency level',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
        ]);

        $handler = new FieldCommandStubHandler($this->validator, $this->doctrine);
        $field   = $handler->getEntity($command);

        self::assertInstanceOf(Field::class, $field);
        self::assertEquals($template->getId(), $field->getTemplate()->getId());
        self::assertNull($field->getState());
        self::assertEquals('Priority', $field->getName());
        self::assertEquals('Urgency level', $field->getDescription());
        self::assertTrue($field->isRequired());
        self::assertFalse($field->hasGuestAccess());
        self::assertFalse($field->getShowInEmails());
        self::assertEquals(1, $field->getIndexNumber());
    }

    public function testCreateByStateSuccess()
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

        $handler = new FieldCommandStubHandler($this->validator, $this->doctrine);
        $field   = $handler->getEntity($command);

        self::assertInstanceOf(Field::class, $field);
        self::assertEquals($state->getTemplateId(), $field->getTemplate()->getId());
        self::assertEquals($state->getId(), $field->getState()->getId());
        self::assertEquals('Priority', $field->getName());
        self::assertEquals('Urgency level', $field->getDescription());
        self::assertTrue($field->isRequired());
        self::assertFalse($field->hasGuestAccess());
        self::assertFalse($field->getShowInEmails());
        self::assertEquals(5, $field->getIndexNumber());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown template.
     */
    public function testCreateByTemplateNotFound()
    {
        $command = new CreateFieldBaseCommand([
            'template'     => $this->getMaxId(),
            'name'         => 'Priority',
            'description'  => 'Urgency level',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
        ]);

        $handler = new FieldCommandStubHandler($this->validator, $this->doctrine);
        $handler->getEntity($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testCreateByStateNotFound()
    {
        $command = new CreateFieldBaseCommand([
            'state'        => $this->getMaxId(),
            'name'         => 'Priority',
            'description'  => 'Urgency level',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
        ]);

        $handler = new FieldCommandStubHandler($this->validator, $this->doctrine);
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

        $handler = new FieldCommandStubHandler($this->validator, $this->doctrine);
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

        $handler = new FieldCommandStubHandler($this->validator, $this->doctrine);
        $entity  = $handler->getEntity($command);

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
            'id'           => $this->getMaxId(),
            'name'         => 'Team',
            'description'  => 'New description',
            'required'     => false,
            'guestAccess'  => true,
            'showInEmails' => true,
        ]);

        $handler = new FieldCommandStubHandler($this->validator, $this->doctrine);
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

        $handler = new FieldCommandStubHandler($this->validator, $this->doctrine);
        $handler->getEntity($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Unsupported command.
     */
    public function testUnsupportedCommand()
    {
        $command = null;

        $handler = new FieldCommandStubHandler($this->validator, $this->doctrine);
        $handler->getEntity($command);
    }
}
