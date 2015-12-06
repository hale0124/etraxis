<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields;

use eTraxis\Entity\DecimalValue;
use eTraxis\Entity\Field;
use eTraxis\Tests\BaseTestCase;

class UpdateDecimalFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy(['name' => 'U.S. viewers']);

        /** @var DecimalValue $minValue */
        /** @var DecimalValue $maxValue */
        /** @var DecimalValue $default */
        $minValue = $this->doctrine->getRepository('eTraxis:DecimalValue')->find($field->getParameter1());
        $maxValue = $this->doctrine->getRepository('eTraxis:DecimalValue')->find($field->getParameter2());

        $this->assertEquals(Field::TYPE_DECIMAL, $field->getType());
        $this->assertEquals('U.S. viewers', $field->getName());
        $this->assertNull($field->getDescription());
        $this->assertFalse($field->isRequired());
        $this->assertFalse($field->hasGuestAccess());
        $this->assertFalse($field->getShowInEmails());
        $this->assertEquals('0.0', $minValue->getValue());
        $this->assertEquals('10.0', $maxValue->getValue());
        $this->assertNull($field->getDefaultValue());

        $command = new UpdateDecimalFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Total U.S. viewers',
            'description'  => '(millions)',
            'required'     => true,
            'guestAccess'  => true,
            'showInEmails' => true,
            'minValue'     => '0.1',
            'maxValue'     => '50.0',
            'default'      => '10.0',
        ]);

        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository('eTraxis:Field')->find($field->getId());

        $minValue = $this->doctrine->getRepository('eTraxis:DecimalValue')->find($field->getParameter1());
        $maxValue = $this->doctrine->getRepository('eTraxis:DecimalValue')->find($field->getParameter2());
        $default  = $this->doctrine->getRepository('eTraxis:DecimalValue')->find($field->getDefaultValue());

        $this->assertEquals(Field::TYPE_DECIMAL, $field->getType());
        $this->assertEquals('Total U.S. viewers', $field->getName());
        $this->assertEquals('(millions)', $field->getDescription());
        $this->assertTrue($field->isRequired());
        $this->assertTrue($field->hasGuestAccess());
        $this->assertTrue($field->getShowInEmails());
        $this->assertEquals('0.1', $minValue->getValue());
        $this->assertEquals('50.0', $maxValue->getValue());
        $this->assertEquals('10.0', $default->getValue());
    }

    /**
     * @expectedException \eTraxis\SimpleBus\Middleware\ValidationException
     * @expectedExceptionMessage Maximum value should be greater then minimum one.
     */
    public function testMinMaxValues()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy(['name' => 'U.S. viewers']);

        $this->assertNotNull($field);

        $command = new UpdateDecimalFieldCommand([
            'id'           => $field->getId(),
            'name'         => $field->getName(),
            'required'     => $field->isRequired(),
            'guestAccess'  => $field->hasGuestAccess(),
            'showInEmails' => $field->getShowInEmails(),
            'minValue'     => '10.0',
            'maxValue'     => '0.0',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\SimpleBus\Middleware\ValidationException
     * @expectedExceptionMessage Default value should be in range from 0.00 to 100.00.
     */
    public function testDefaultValue()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy(['name' => 'U.S. viewers']);

        $this->assertNotNull($field);

        $command = new UpdateDecimalFieldCommand([
            'id'           => $field->getId(),
            'name'         => $field->getName(),
            'required'     => $field->isRequired(),
            'guestAccess'  => $field->hasGuestAccess(),
            'showInEmails' => $field->getShowInEmails(),
            'minValue'     => '0.00',
            'maxValue'     => '100.00',
            'default'      => '101',
        ]);

        $this->command_bus->handle($command);
    }
}
