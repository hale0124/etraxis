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
use eTraxis\SimpleBus\Fields\Handler\CreateFieldCommandHandler;
use eTraxis\Tests\TransactionalTestCase;

class CreateFieldCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $command = new CreateRecordFieldCommand([
            'state'       => $state->getId(),
            'name'        => 'Ref ID',
            'description' => 'Reference',
            'required'    => true,
        ]);

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        $handler = new CreateFieldCommandHandler($this->validator, $manager, $this->translator);
        $handler->handle($command);

        $manager->flush();

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => $command->name]);

        self::assertInstanceOf(Field::class, $field);
        self::assertEquals(Field::TYPE_RECORD, $field->getType());
        self::assertEquals($state, $field->getState());
        self::assertEquals('Ref ID', $field->getName());
        self::assertEquals('Reference', $field->getDescription());
        self::assertEquals(5, $field->getIndexNumber());
        self::assertTrue($field->isRequired());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Unsupported command.
     */
    public function testUnsupportedCommand()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        $command = new UpdateRecordFieldCommand([
            'id'          => $field->getId(),
            'name'        => 'Ref ID',
            'description' => 'Reference',
            'required'    => true,
        ]);

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        $handler = new CreateFieldCommandHandler($this->validator, $manager, $this->translator);
        $handler->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testNotFound()
    {
        $command = new CreateRecordFieldCommand([
            'state'       => self::UNKNOWN_ENTITY_ID,
            'name'        => 'Ref ID',
            'description' => 'Reference',
            'required'    => true,
        ]);

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        $handler = new CreateFieldCommandHandler($this->validator, $manager, $this->translator);
        $handler->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Field with entered name already exists.
     */
    public function testConflict()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $command = new CreateRecordFieldCommand([
            'state'       => $state->getId(),
            'name'        => 'Notes',
            'description' => 'Reference',
            'required'    => true,
        ]);

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        $handler = new CreateFieldCommandHandler($this->validator, $manager, $this->translator);
        $handler->handle($command);
    }
}
