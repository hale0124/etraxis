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
use eTraxis\SimpleBus\Fields\Handler\UpdateFieldCommandHandler;
use eTraxis\Tests\BaseTestCase;

class UpdateFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        $command = new UpdateStringFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Team',
            'description'  => 'New description',
            'required'     => false,
            'showInEmails' => true,
            'maxLength'    => $field->asString()->getMaxLength(),
        ]);

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        $handler = new UpdateFieldCommandHandler($this->validator, $manager, $this->translator);
        $handler->handle($command);

        $manager->flush();

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->find($command->id);

        self::assertInstanceOf(Field::class, $field);
        self::assertEquals('Team', $field->getName());
        self::assertEquals('New description', $field->getDescription());
        self::assertFalse($field->isRequired());
        self::assertTrue($field->getShowInEmails());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Unsupported command.
     */
    public function testUnsupportedCommand()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $command = new CreateRecordFieldCommand([
            'state'        => $state->getId(),
            'name'         => 'Ref ID',
            'description'  => 'Reference',
            'required'     => true,
            'showInEmails' => false,
        ]);

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        $handler = new UpdateFieldCommandHandler($this->validator, $manager, $this->translator);
        $handler->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown field.
     */
    public function testNotFound()
    {
        $command = new UpdateStringFieldCommand([
            'id'           => self::UNKNOWN_ENTITY_ID,
            'name'         => 'Team',
            'description'  => 'New description',
            'required'     => false,
            'showInEmails' => true,
        ]);

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        $handler = new UpdateFieldCommandHandler($this->validator, $manager, $this->translator);
        $handler->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Field with entered name already exists.
     */
    public function testConflict()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        $command = new UpdateStringFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Notes',
            'description'  => 'New description',
            'required'     => false,
            'showInEmails' => true,
        ]);

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        $handler = new UpdateFieldCommandHandler($this->validator, $manager, $this->translator);
        $handler->handle($command);
    }
}
