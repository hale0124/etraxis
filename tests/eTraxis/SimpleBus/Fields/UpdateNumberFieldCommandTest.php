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

class UpdateNumberFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Episode']);

        $this->assertEquals(Field::TYPE_NUMBER, $field->getType());
        $this->assertEquals('Episode', $field->getName());
        $this->assertNull($field->getDescription());
        $this->assertTrue($field->isRequired());
        $this->assertTrue($field->hasGuestAccess());
        $this->assertFalse($field->getShowInEmails());
        $this->assertEquals(1, $field->getParameter1());
        $this->assertEquals(100, $field->getParameter2());
        $this->assertNull($field->getDefaultValue());

        $command = new UpdateNumberFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Episode #',
            'description'  => 'ID of the episode',
            'required'     => false,
            'guestAccess'  => false,
            'showInEmails' => true,
            'minValue'     => 0,
            'maxValue'     => 50,
            'defaultValue' => 1,
        ]);

        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->find($field->getId());

        $this->assertEquals(Field::TYPE_NUMBER, $field->getType());
        $this->assertEquals('Episode #', $field->getName());
        $this->assertEquals('ID of the episode', $field->getDescription());
        $this->assertFalse($field->isRequired());
        $this->assertFalse($field->hasGuestAccess());
        $this->assertTrue($field->getShowInEmails());
        $this->assertEquals(0, $field->getParameter1());
        $this->assertEquals(50, $field->getParameter2());
        $this->assertEquals(1, $field->getDefaultValue());
    }

    /**
     * @expectedException \SimpleBus\ValidationException
     * @expectedExceptionMessage Maximum value should be greater then minimum one.
     */
    public function testMinMaxValues()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Episode']);

        $this->assertNotNull($field);

        $command = new UpdateNumberFieldCommand([
            'id'           => $field->getId(),
            'name'         => $field->getName(),
            'required'     => $field->isRequired(),
            'guestAccess'  => $field->hasGuestAccess(),
            'showInEmails' => $field->getShowInEmails(),
            'minValue'     => 100,
            'maxValue'     => 1,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \SimpleBus\ValidationException
     * @expectedExceptionMessage Default value should be in range from 1 to 100.
     */
    public function testDefaultValue()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Episode']);

        $this->assertNotNull($field);

        $command = new UpdateNumberFieldCommand([
            'id'           => $field->getId(),
            'name'         => $field->getName(),
            'required'     => $field->isRequired(),
            'guestAccess'  => $field->hasGuestAccess(),
            'showInEmails' => $field->getShowInEmails(),
            'minValue'     => 1,
            'maxValue'     => 100,
            'defaultValue' => 0,
        ]);

        $this->command_bus->handle($command);
    }
}
