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

use eTraxis\Entity\Field;
use eTraxis\Tests\BaseTestCase;

class UpdateStringFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy(['name' => 'Production code']);

        $this->assertEquals(Field::TYPE_STRING, $field->getType());
        $this->assertEquals('Production code', $field->getName());
        $this->assertNull($field->getDescription());
        $this->assertTrue($field->isRequired());
        $this->assertTrue($field->hasGuestAccess());
        $this->assertFalse($field->getShowInEmails());
        $this->assertEquals(7, $field->getParameter1());
        $this->assertNull($field->getDefaultValue());
        $this->assertNull($field->getRegexCheck());
        $this->assertNull($field->getRegexSearch());
        $this->assertNull($field->getRegexReplace());

        $command = new UpdateStringFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Code',
            'description'  => '(millions)',
            'required'     => false,
            'guestAccess'  => false,
            'showInEmails' => true,
            'maxLength'    => 6,
            'default'      => '?ACV??',
            'regexCheck'   => '^(\d{1})ACV(\d{2})$',
            'regexSearch'  => '^(\d{1})ACV(\d{2})$',
            'regexReplace' => 'Season $1, Episode $2',
        ]);

        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository('eTraxis:Field')->find($field->getId());

        $default = $this->doctrine->getRepository('eTraxis:StringValue')->find($field->getDefaultValue());

        $this->assertEquals(Field::TYPE_STRING, $field->getType());
        $this->assertEquals('Code', $field->getName());
        $this->assertEquals('(millions)', $field->getDescription());
        $this->assertFalse($field->isRequired());
        $this->assertFalse($field->hasGuestAccess());
        $this->assertTrue($field->getShowInEmails());
        $this->assertEquals(6, $field->getParameter1());
        $this->assertEquals('?ACV??', $default->getValue());
        $this->assertEquals('^(\d{1})ACV(\d{2})$', $field->getRegexCheck());
        $this->assertEquals('^(\d{1})ACV(\d{2})$', $field->getRegexSearch());
        $this->assertEquals('Season $1, Episode $2', $field->getRegexReplace());
    }
}
