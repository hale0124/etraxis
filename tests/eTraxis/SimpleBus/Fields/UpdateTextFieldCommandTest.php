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

        self::assertEquals(Field::TYPE_TEXT, $field->getType());
        self::assertEquals('Plot', $field->getName());
        self::assertNull($field->getDescription());
        self::assertTrue($field->isRequired());
        self::assertFalse($field->getShowInEmails());
        self::assertEquals(2000, $field->getParameters()->getParameter1());
        self::assertNull($field->getParameters()->getDefaultValue());
        self::assertNull($field->getRegex()->getCheck());
        self::assertNull($field->getRegex()->getSearch());
        self::assertNull($field->getRegex()->getReplace());

        $command = new UpdateTextFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Story',
            'description'  => 'spoiler!',
            'required'     => false,
            'showInEmails' => true,
            'maxLength'    => 1000,
            'defaultValue' => 'TBD',
            'regexCheck'   => '^(.+)$',
            'regexSearch'  => '^(.+)$',
            'regexReplace' => '$1',
        ]);

        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->find($field->getId());

        $default = $this->doctrine->getRepository(TextValue::class)->find($field->getParameters()->getDefaultValue());

        self::assertEquals(Field::TYPE_TEXT, $field->getType());
        self::assertEquals('Story', $field->getName());
        self::assertEquals('spoiler!', $field->getDescription());
        self::assertFalse($field->isRequired());
        self::assertTrue($field->getShowInEmails());
        self::assertEquals(1000, $field->getParameters()->getParameter1());
        self::assertEquals('TBD', $default->getValue());
        self::assertEquals('^(.+)$', $field->getRegex()->getCheck());
        self::assertEquals('^(.+)$', $field->getRegex()->getSearch());
        self::assertEquals('$1', $field->getRegex()->getReplace());
    }
}
