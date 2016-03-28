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
use eTraxis\Entity\StringValue;
use eTraxis\Tests\BaseTestCase;

class UpdateStringFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Production code']);

        self::assertEquals(Field::TYPE_STRING, $field->getType());
        self::assertEquals('Production code', $field->getName());
        self::assertNull($field->getDescription());
        self::assertTrue($field->isRequired());
        self::assertTrue($field->hasGuestAccess());
        self::assertFalse($field->getShowInEmails());
        self::assertEquals(7, $field->getParameter1());
        self::assertNull($field->getDefaultValue());
        self::assertNull($field->getRegexCheck());
        self::assertNull($field->getRegexSearch());
        self::assertNull($field->getRegexReplace());

        $command = new UpdateStringFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Code',
            'description'  => '(millions)',
            'required'     => false,
            'guestAccess'  => false,
            'showInEmails' => true,
            'maxLength'    => 6,
            'defaultValue' => '?ACV??',
            'regexCheck'   => '^(\d{1})ACV(\d{2})$',
            'regexSearch'  => '^(\d{1})ACV(\d{2})$',
            'regexReplace' => 'Season $1, Episode $2',
        ]);

        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->find($field->getId());

        $default = $this->doctrine->getRepository(StringValue::class)->find($field->getDefaultValue());

        self::assertEquals(Field::TYPE_STRING, $field->getType());
        self::assertEquals('Code', $field->getName());
        self::assertEquals('(millions)', $field->getDescription());
        self::assertFalse($field->isRequired());
        self::assertFalse($field->hasGuestAccess());
        self::assertTrue($field->getShowInEmails());
        self::assertEquals(6, $field->getParameter1());
        self::assertEquals('?ACV??', $default->getValue());
        self::assertEquals('^(\d{1})ACV(\d{2})$', $field->getRegexCheck());
        self::assertEquals('^(\d{1})ACV(\d{2})$', $field->getRegexSearch());
        self::assertEquals('Season $1, Episode $2', $field->getRegexReplace());
    }
}
