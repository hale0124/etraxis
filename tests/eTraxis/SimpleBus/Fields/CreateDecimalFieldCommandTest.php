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
use eTraxis\Tests\BaseTestCase;

class CreateDecimalFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var \eTraxis\Entity\State $state */
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'New']);

        $this->assertNotNull($state);

        $command = new CreateDecimalFieldCommand([
            'template'     => $state->getTemplateId(),
            'state'        => $state->getId(),
            'name'         => 'Price',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
            'minValue'     => '0.00',
            'maxValue'     => '100000.00',
            'default'      => '499.95',
        ]);

        $this->command_bus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy(['name' => $command->name]);

        /** @var DecimalValue $minValue */
        /** @var DecimalValue $maxValue */
        /** @var DecimalValue $default */
        $minValue = $this->doctrine->getRepository('eTraxis:DecimalValue')->find($field->getParameter1());
        $maxValue = $this->doctrine->getRepository('eTraxis:DecimalValue')->find($field->getParameter2());
        $default  = $this->doctrine->getRepository('eTraxis:DecimalValue')->find($field->getDefaultValue());

        $this->assertInstanceOf('\eTraxis\Entity\Field', $field);
        $this->assertEquals(Field::TYPE_DECIMAL, $field->getType());
        $this->assertEquals('0.00', $minValue->getValue());
        $this->assertEquals('100000.0', $maxValue->getValue());
        $this->assertEquals('499.95', $default->getValue());
    }

    /**
     * @expectedException \eTraxis\SimpleBus\Middleware\ValidationException
     * @expectedExceptionMessage Maximum value should be greater then minimum one.
     */
    public function testMinMaxValues()
    {
        /** @var \eTraxis\Entity\State $state */
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'New']);

        $this->assertNotNull($state);

        $command = new CreateDecimalFieldCommand([
            'template'     => $state->getTemplateId(),
            'state'        => $state->getId(),
            'name'         => 'Month',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
            'minValue'     => '43.21',
            'maxValue'     => '12.34',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\SimpleBus\Middleware\ValidationException
     * @expectedExceptionMessage Default value should be in range from 1 to 100.
     */
    public function testDefaultValue()
    {
        /** @var \eTraxis\Entity\State $state */
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'New']);

        $this->assertNotNull($state);

        $command = new CreateDecimalFieldCommand([
            'template'     => $state->getTemplateId(),
            'state'        => $state->getId(),
            'name'         => 'Month',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
            'minValue'     => '1',
            'maxValue'     => '100',
            'default'      => '0.99',
        ]);

        $this->command_bus->handle($command);
    }
}
