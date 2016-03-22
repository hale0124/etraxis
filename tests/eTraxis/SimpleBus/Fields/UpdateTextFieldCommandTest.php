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
use eTraxis\Entity\TextValue;
use eTraxis\Tests\BaseTestCase;

class UpdateTextFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Plot']);

        $this->assertEquals(Field::TYPE_TEXT, $field->getType());
        $this->assertEquals('Plot', $field->getName());
        $this->assertNull($field->getDescription());
        $this->assertTrue($field->isRequired());
        $this->assertTrue($field->hasGuestAccess());
        $this->assertFalse($field->getShowInEmails());
        $this->assertEquals(2000, $field->getParameter1());
        $this->assertNull($field->getDefaultValue());
        $this->assertNull($field->getRegexCheck());
        $this->assertNull($field->getRegexSearch());
        $this->assertNull($field->getRegexReplace());

        $command = new UpdateTextFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Story',
            'description'  => 'spoiler!',
            'required'     => false,
            'guestAccess'  => false,
            'showInEmails' => true,
            'maxLength'    => 1000,
            'defaultValue' => 'TBD',
            'regexCheck'   => '^(.+)$',
            'regexSearch'  => '^(.+)$',
            'regexReplace' => '$1',
        ]);

        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->find($field->getId());

        $default = $this->doctrine->getRepository(TextValue::class)->find($field->getDefaultValue());

        $this->assertEquals(Field::TYPE_TEXT, $field->getType());
        $this->assertEquals('Story', $field->getName());
        $this->assertEquals('spoiler!', $field->getDescription());
        $this->assertFalse($field->isRequired());
        $this->assertFalse($field->hasGuestAccess());
        $this->assertTrue($field->getShowInEmails());
        $this->assertEquals(1000, $field->getParameter1());
        $this->assertEquals('TBD', $default->getValue());
        $this->assertEquals('^(.+)$', $field->getRegexCheck());
        $this->assertEquals('^(.+)$', $field->getRegexSearch());
        $this->assertEquals('$1', $field->getRegexReplace());
    }
}
