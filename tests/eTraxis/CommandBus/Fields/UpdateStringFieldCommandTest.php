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
use eTraxis\Entity\StringValue;
use eTraxis\Tests\TransactionalTestCase;

class UpdateStringFieldCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Production code']);

        self::assertEquals(FieldType::STRING, $field->getType());
        self::assertEquals('Production code', $field->getName());
        self::assertNull($field->getDescription());
        self::assertTrue($field->isRequired());
        self::assertEquals(7, $field->getParameters()->getParameter1());
        self::assertNull($field->getParameters()->getDefaultValue());
        self::assertNull($field->getPCRE()->getCheck());
        self::assertNull($field->getPCRE()->getSearch());
        self::assertNull($field->getPCRE()->getReplace());

        $command = new UpdateStringFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Code',
            'description'  => '(millions)',
            'required'     => false,
            'maxLength'    => 6,
            'defaultValue' => '?ACV??',
            'pcreCheck'    => '^(\d{1})ACV(\d{2})$',
            'pcreSearch'   => '^(\d{1})ACV(\d{2})$',
            'pcreReplace'  => 'Season $1, Episode $2',
        ]);

        $this->commandbus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->find($field->getId());

        $default = $this->doctrine->getRepository(StringValue::class)->find($field->getParameters()->getDefaultValue());

        self::assertEquals(FieldType::STRING, $field->getType());
        self::assertEquals('Code', $field->getName());
        self::assertEquals('(millions)', $field->getDescription());
        self::assertFalse($field->isRequired());
        self::assertEquals(6, $field->getParameters()->getParameter1());
        self::assertEquals('?ACV??', $default->getValue());
        self::assertEquals('^(\d{1})ACV(\d{2})$', $field->getPCRE()->getCheck());
        self::assertEquals('^(\d{1})ACV(\d{2})$', $field->getPCRE()->getSearch());
        self::assertEquals('Season $1, Episode $2', $field->getPCRE()->getReplace());
    }
}
