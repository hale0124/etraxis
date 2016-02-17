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

class UpdateDateFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Original air date']);

        $this->assertEquals(Field::TYPE_DATE, $field->getType());
        $this->assertEquals('Original air date', $field->getName());
        $this->assertNull($field->getDescription());
        $this->assertTrue($field->isRequired());
        $this->assertTrue($field->hasGuestAccess());
        $this->assertFalse($field->getShowInEmails());
        $this->assertEquals(0, $field->getParameter1());
        $this->assertEquals(7, $field->getParameter2());
        $this->assertNull($field->getDefaultValue());

        $command = new UpdateDateFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Release date',
            'description'  => 'Date of the release',
            'required'     => false,
            'guestAccess'  => false,
            'showInEmails' => true,
            'minValue'     => 1,
            'maxValue'     => 14,
            'default'      => 10,
        ]);

        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->find($field->getId());

        $this->assertEquals(Field::TYPE_DATE, $field->getType());
        $this->assertEquals('Release date', $field->getName());
        $this->assertEquals('Date of the release', $field->getDescription());
        $this->assertFalse($field->isRequired());
        $this->assertFalse($field->hasGuestAccess());
        $this->assertTrue($field->getShowInEmails());
        $this->assertEquals(1, $field->getParameter1());
        $this->assertEquals(14, $field->getParameter2());
        $this->assertEquals(10, $field->getDefaultValue());
    }

    /**
     * @expectedException \eTraxis\SimpleBus\Middleware\ValidationException
     * @expectedExceptionMessage Maximum value should be greater then minimum one.
     */
    public function testMinMaxValues()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Original air date']);

        $this->assertNotNull($field);

        $command = new UpdateDateFieldCommand([
            'id'           => $field->getId(),
            'name'         => $field->getName(),
            'required'     => $field->isRequired(),
            'guestAccess'  => $field->hasGuestAccess(),
            'showInEmails' => $field->getShowInEmails(),
            'minValue'     => 7,
            'maxValue'     => 0,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\SimpleBus\Middleware\ValidationException
     * @expectedExceptionMessage Default value should be in range from 0 to 7.
     */
    public function testDefaultValue()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Original air date']);

        $this->assertNotNull($field);

        $command = new UpdateDateFieldCommand([
            'id'           => $field->getId(),
            'name'         => $field->getName(),
            'required'     => $field->isRequired(),
            'guestAccess'  => $field->hasGuestAccess(),
            'showInEmails' => $field->getShowInEmails(),
            'minValue'     => 0,
            'maxValue'     => 7,
            'default'      => 10,
        ]);

        $this->command_bus->handle($command);
    }
}
