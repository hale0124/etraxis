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

class CreateDurationFieldCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $command = new CreateDurationFieldCommand([
            'state'        => $state->getId(),
            'name'         => 'Duration',
            'required'     => true,
            'minValue'     => '0:00',
            'maxValue'     => '168:00',
            'defaultValue' => '48:00',
        ]);

        $this->commandbus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => $command->name]);

        self::assertInstanceOf(Field::class, $field);
        self::assertEquals(FieldType::DURATION, $field->getType());
        self::assertEquals(0, $field->getParameters()->getParameter1());
        self::assertEquals(10080, $field->getParameters()->getParameter2());
        self::assertEquals(2880, $field->getParameters()->getDefaultValue());
    }

    /**
     * @expectedException \eTraxis\CommandBus\Middleware\ValidationException
     * @expectedExceptionMessage Maximum value should be greater then minimum one.
     */
    public function testMinMaxValues()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $command = new CreateDurationFieldCommand([
            'state'    => $state->getId(),
            'name'     => 'Duration',
            'required' => true,
            'minValue' => '168:00',
            'maxValue' => '0:00',
        ]);

        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \eTraxis\CommandBus\Middleware\ValidationException
     * @expectedExceptionMessage Default value should be in range from 0:00 to 168:00.
     */
    public function testDefaultValue()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $command = new CreateDurationFieldCommand([
            'state'        => $state->getId(),
            'name'         => 'Duration',
            'required'     => true,
            'minValue'     => '0:00',
            'maxValue'     => '168:00',
            'defaultValue' => '240:00',
        ]);

        $this->commandbus->handle($command);
    }
}
