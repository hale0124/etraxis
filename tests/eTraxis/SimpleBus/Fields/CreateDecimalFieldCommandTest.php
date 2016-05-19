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

use eTraxis\Entity\DecimalValue;
use eTraxis\Entity\Field;
use eTraxis\Entity\State;
use eTraxis\Tests\BaseTestCase;

class CreateDecimalFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $command = new CreateDecimalFieldCommand([
            'state'        => $state->getId(),
            'name'         => 'Price',
            'required'     => true,
            'showInEmails' => false,
            'minValue'     => '0.00',
            'maxValue'     => '100000.00',
            'defaultValue' => '499.95',
        ]);

        $this->command_bus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => $command->name]);

        /** @var DecimalValue $minValue */
        /** @var DecimalValue $maxValue */
        /** @var DecimalValue $default */
        $minValue = $this->doctrine->getRepository(DecimalValue::class)->find($field->getParameters()->getParameter1());
        $maxValue = $this->doctrine->getRepository(DecimalValue::class)->find($field->getParameters()->getParameter2());
        $default  = $this->doctrine->getRepository(DecimalValue::class)->find($field->getParameters()->getDefaultValue());

        self::assertInstanceOf(Field::class, $field);
        self::assertEquals(Field::TYPE_DECIMAL, $field->getType());
        self::assertEquals('0.00', $minValue->getValue());
        self::assertEquals('100000.0', $maxValue->getValue());
        self::assertEquals('499.95', $default->getValue());
    }

    /**
     * @expectedException \SimpleBus\ValidationException
     * @expectedExceptionMessage Maximum value should be greater then minimum one.
     */
    public function testMinMaxValues()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $command = new CreateDecimalFieldCommand([
            'state'        => $state->getId(),
            'name'         => 'Month',
            'required'     => true,
            'showInEmails' => false,
            'minValue'     => '43.21',
            'maxValue'     => '12.34',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \SimpleBus\ValidationException
     * @expectedExceptionMessage Default value should be in range from 1 to 100.
     */
    public function testDefaultValue()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $command = new CreateDecimalFieldCommand([
            'state'        => $state->getId(),
            'name'         => 'Month',
            'required'     => true,
            'showInEmails' => false,
            'minValue'     => '1',
            'maxValue'     => '100',
            'defaultValue' => '0.99',
        ]);

        $this->command_bus->handle($command);
    }
}
