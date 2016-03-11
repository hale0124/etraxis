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

class UpdateCheckboxFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Multipart']);

        $this->assertEquals(Field::TYPE_CHECKBOX, $field->getType());
        $this->assertEquals('Multipart', $field->getName());
        $this->assertNull($field->getDescription());
        $this->assertTrue($field->isRequired());
        $this->assertFalse($field->hasGuestAccess());
        $this->assertFalse($field->getShowInEmails());
        $this->assertNull($field->getDefaultValue());

        $command = new UpdateCheckboxFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Is multipart',
            'description'  => 'Whether is multipart',
            'required'     => false,
            'guestAccess'  => true,
            'showInEmails' => true,
            'default'      => true,
        ]);

        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->find($field->getId());

        $this->assertEquals(Field::TYPE_CHECKBOX, $field->getType());
        $this->assertEquals('Is multipart', $field->getName());
        $this->assertEquals('Whether is multipart', $field->getDescription());
        $this->assertFalse($field->isRequired());
        $this->assertTrue($field->hasGuestAccess());
        $this->assertTrue($field->getShowInEmails());
        $this->assertTrue($field->asCheckbox()->getDefaultValue());
    }
}
