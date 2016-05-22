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
use eTraxis\Tests\TransactionalTestCase;

class UpdateCheckboxFieldCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Multipart']);

        self::assertEquals(Field::TYPE_CHECKBOX, $field->getType());
        self::assertEquals('Multipart', $field->getName());
        self::assertNull($field->getDescription());
        self::assertTrue($field->isRequired());
        self::assertNull($field->getParameters()->getDefaultValue());

        $command = new UpdateCheckboxFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Is multipart',
            'description'  => 'Whether is multipart',
            'required'     => false,
            'defaultValue' => true,
        ]);

        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->find($field->getId());

        self::assertEquals(Field::TYPE_CHECKBOX, $field->getType());
        self::assertEquals('Is multipart', $field->getName());
        self::assertEquals('Whether is multipart', $field->getDescription());
        self::assertFalse($field->isRequired());
        self::assertTrue($field->asCheckbox()->getDefaultValue());
    }
}
