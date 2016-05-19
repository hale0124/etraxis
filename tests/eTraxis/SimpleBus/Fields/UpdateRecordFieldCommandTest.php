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

class UpdateRecordFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Delivery']);

        self::assertEquals(Field::TYPE_RECORD, $field->getType());
        self::assertEquals('Delivery', $field->getName());
        self::assertNull($field->getDescription());
        self::assertFalse($field->isRequired());
        self::assertFalse($field->getShowInEmails());

        $command = new UpdateRecordFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Delivery #',
            'description'  => 'ID of the delivery task',
            'required'     => true,
            'showInEmails' => true,
        ]);

        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->find($field->getId());

        self::assertEquals(Field::TYPE_RECORD, $field->getType());
        self::assertEquals('Delivery #', $field->getName());
        self::assertEquals('ID of the delivery task', $field->getDescription());
        self::assertTrue($field->isRequired());
        self::assertTrue($field->getShowInEmails());
    }
}
