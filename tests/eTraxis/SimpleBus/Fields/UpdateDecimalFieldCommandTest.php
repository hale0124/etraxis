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
use eTraxis\Tests\TransactionalTestCase;

class UpdateDecimalFieldCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'U.S. viewers']);

        /** @var DecimalValue $minValue */
        /** @var DecimalValue $maxValue */
        /** @var DecimalValue $default */
        $minValue = $this->doctrine->getRepository(DecimalValue::class)->find($field->getParameters()->getParameter1());
        $maxValue = $this->doctrine->getRepository(DecimalValue::class)->find($field->getParameters()->getParameter2());

        self::assertEquals(Field::TYPE_DECIMAL, $field->getType());
        self::assertEquals('U.S. viewers', $field->getName());
        self::assertNull($field->getDescription());
        self::assertFalse($field->isRequired());
        self::assertEquals('0.0', $minValue->getValue());
        self::assertEquals('10.0', $maxValue->getValue());
        self::assertNull($field->getParameters()->getDefaultValue());

        $command = new UpdateDecimalFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Total U.S. viewers',
            'description'  => '(millions)',
            'required'     => true,
            'minValue'     => '0.1',
            'maxValue'     => '50.0',
            'defaultValue' => '10.0',
        ]);

        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->find($field->getId());

        $minValue = $this->doctrine->getRepository(DecimalValue::class)->find($field->getParameters()->getParameter1());
        $maxValue = $this->doctrine->getRepository(DecimalValue::class)->find($field->getParameters()->getParameter2());
        $default  = $this->doctrine->getRepository(DecimalValue::class)->find($field->getParameters()->getDefaultValue());

        self::assertEquals(Field::TYPE_DECIMAL, $field->getType());
        self::assertEquals('Total U.S. viewers', $field->getName());
        self::assertEquals('(millions)', $field->getDescription());
        self::assertTrue($field->isRequired());
        self::assertEquals('0.1', $minValue->getValue());
        self::assertEquals('50.0', $maxValue->getValue());
        self::assertEquals('10.0', $default->getValue());
    }

    /**
     * @expectedException \SimpleBus\ValidationException
     * @expectedExceptionMessage Maximum value should be greater then minimum one.
     */
    public function testMinMaxValues()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'U.S. viewers']);

        $command = new UpdateDecimalFieldCommand([
            'id'       => $field->getId(),
            'name'     => $field->getName(),
            'required' => $field->isRequired(),
            'minValue' => '10.0',
            'maxValue' => '0.0',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \SimpleBus\ValidationException
     * @expectedExceptionMessage Default value should be in range from 0.00 to 100.00.
     */
    public function testDefaultValue()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'U.S. viewers']);

        $command = new UpdateDecimalFieldCommand([
            'id'           => $field->getId(),
            'name'         => $field->getName(),
            'required'     => $field->isRequired(),
            'minValue'     => '0.00',
            'maxValue'     => '100.00',
            'defaultValue' => '101',
        ]);

        $this->command_bus->handle($command);
    }
}
