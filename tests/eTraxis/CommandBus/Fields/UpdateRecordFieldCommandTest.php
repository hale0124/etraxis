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
use eTraxis\Tests\TransactionalTestCase;

class UpdateRecordFieldCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Delivery']);

        self::assertEquals(FieldType::RECORD, $field->getType());
        self::assertEquals('Delivery', $field->getName());
        self::assertNull($field->getDescription());
        self::assertFalse($field->isRequired());

        $command = new UpdateRecordFieldCommand([
            'id'          => $field->getId(),
            'name'        => 'Delivery #',
            'description' => 'ID of the delivery task',
            'required'    => true,
        ]);

        $this->commandbus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->find($field->getId());

        self::assertEquals(FieldType::RECORD, $field->getType());
        self::assertEquals('Delivery #', $field->getName());
        self::assertEquals('ID of the delivery task', $field->getDescription());
        self::assertTrue($field->isRequired());
    }
}
