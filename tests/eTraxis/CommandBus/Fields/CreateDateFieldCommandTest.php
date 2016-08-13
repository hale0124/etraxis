<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Fields;

use eTraxis\Dictionary\FieldType;
use eTraxis\Entity\Field;
use eTraxis\Entity\State;
use eTraxis\Tests\TransactionalTestCase;

class CreateDateFieldCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $command = new CreateDateFieldCommand([
            'state'        => $state->getId(),
            'name'         => 'Deadline',
            'required'     => true,
            'minValue'     => 1,
            'maxValue'     => 7,
            'defaultValue' => 2,
        ]);

        $this->commandbus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => $command->name]);

        self::assertInstanceOf(Field::class, $field);
        self::assertEquals(FieldType::DATE, $field->getType());
        self::assertEquals(1, $field->getParameters()->getParameter1());
        self::assertEquals(7, $field->getParameters()->getParameter2());
        self::assertEquals(2, $field->getParameters()->getDefaultValue());
    }

    /**
     * @expectedException \eTraxis\CommandBus\Middleware\ValidationException
     * @expectedExceptionMessage Maximum value should be greater then minimum one.
     */
    public function testMinMaxValues()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $command = new CreateDateFieldCommand([
            'state'    => $state->getId(),
            'name'     => 'Deadline',
            'required' => true,
            'minValue' => 7,
            'maxValue' => 1,
        ]);

        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \eTraxis\CommandBus\Middleware\ValidationException
     * @expectedExceptionMessage Default value should be in range from 1 to 7.
     */
    public function testDefaultValue()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $command = new CreateDateFieldCommand([
            'state'        => $state->getId(),
            'name'         => 'Deadline',
            'required'     => true,
            'minValue'     => 1,
            'maxValue'     => 7,
            'defaultValue' => 10,
        ]);

        $this->commandbus->handle($command);
    }
}
