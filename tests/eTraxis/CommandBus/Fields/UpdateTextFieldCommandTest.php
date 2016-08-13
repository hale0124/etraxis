<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Fields;

use eTraxis\Dictionary\FieldType;
use eTraxis\Entity\Field;
use eTraxis\Entity\TextValue;
use eTraxis\Tests\TransactionalTestCase;

class UpdateTextFieldCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Plot']);

        self::assertEquals(FieldType::TEXT, $field->getType());
        self::assertEquals('Plot', $field->getName());
        self::assertNull($field->getDescription());
        self::assertTrue($field->isRequired());
        self::assertEquals(2000, $field->getParameters()->getParameter1());
        self::assertNull($field->getParameters()->getDefaultValue());
        self::assertNull($field->getPCRE()->getCheck());
        self::assertNull($field->getPCRE()->getSearch());
        self::assertNull($field->getPCRE()->getReplace());

        $command = new UpdateTextFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Story',
            'description'  => 'spoiler!',
            'required'     => false,
            'maxLength'    => 1000,
            'defaultValue' => 'TBD',
            'pcreCheck'    => '^(.+)$',
            'pcreSearch'   => '^(.+)$',
            'pcreReplace'  => '$1',
        ]);

        $this->commandbus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->find($field->getId());

        $default = $this->doctrine->getRepository(TextValue::class)->find($field->getParameters()->getDefaultValue());

        self::assertEquals(FieldType::TEXT, $field->getType());
        self::assertEquals('Story', $field->getName());
        self::assertEquals('spoiler!', $field->getDescription());
        self::assertFalse($field->isRequired());
        self::assertEquals(1000, $field->getParameters()->getParameter1());
        self::assertEquals('TBD', $default->getValue());
        self::assertEquals('^(.+)$', $field->getPCRE()->getCheck());
        self::assertEquals('^(.+)$', $field->getPCRE()->getSearch());
        self::assertEquals('$1', $field->getPCRE()->getReplace());
    }
}
