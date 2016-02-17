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
use eTraxis\Tests\BaseTestCase;

class UpdateDurationFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Running time']);

        $this->assertEquals(Field::TYPE_DURATION, $field->getType());
        $this->assertEquals('Running time', $field->getName());
        $this->assertNull($field->getDescription());
        $this->assertTrue($field->isRequired());
        $this->assertTrue($field->hasGuestAccess());
        $this->assertFalse($field->getShowInEmails());
        $this->assertEquals(0, $field->getParameter1());
        $this->assertEquals(1440, $field->getParameter2());
        $this->assertNull($field->getDefaultValue());

        $command = new UpdateDurationFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Episode duration',
            'description'  => 'Running time',
            'required'     => false,
            'guestAccess'  => false,
            'showInEmails' => true,
            'minValue'     => '0:01',
            'maxValue'     => '2:00',
            'default'      => '0:22',
        ]);

        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->find($field->getId());

        $this->assertEquals(Field::TYPE_DURATION, $field->getType());
        $this->assertEquals('Episode duration', $field->getName());
        $this->assertEquals('Running time', $field->getDescription());
        $this->assertFalse($field->isRequired());
        $this->assertFalse($field->hasGuestAccess());
        $this->assertTrue($field->getShowInEmails());
        $this->assertEquals(1, $field->getParameter1());
        $this->assertEquals(120, $field->getParameter2());
        $this->assertEquals(22, $field->getDefaultValue());
    }

    /**
     * @expectedException \eTraxis\SimpleBus\Middleware\ValidationException
     * @expectedExceptionMessage Maximum value should be greater then minimum one.
     */
    public function testMinMaxValues()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Running time']);

        $this->assertNotNull($field);

        $command = new UpdateDurationFieldCommand([
            'id'           => $field->getId(),
            'name'         => $field->getName(),
            'required'     => $field->isRequired(),
            'guestAccess'  => $field->hasGuestAccess(),
            'showInEmails' => $field->getShowInEmails(),
            'minValue'     => '24:00',
            'maxValue'     => '0:00',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\SimpleBus\Middleware\ValidationException
     * @expectedExceptionMessage Default value should be in range from 0:00 to 23:59.
     */
    public function testDefaultValue()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Running time']);

        $this->assertNotNull($field);

        $command = new UpdateDurationFieldCommand([
            'id'           => $field->getId(),
            'name'         => $field->getName(),
            'required'     => $field->isRequired(),
            'guestAccess'  => $field->hasGuestAccess(),
            'showInEmails' => $field->getShowInEmails(),
            'minValue'     => '0:00',
            'maxValue'     => '23:59',
            'default'      => '24:00',
        ]);

        $this->command_bus->handle($command);
    }
}
