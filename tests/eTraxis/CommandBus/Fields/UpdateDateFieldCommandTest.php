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

class UpdateDateFieldCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Original air date']);

        self::assertEquals(FieldType::DATE, $field->getType());
        self::assertEquals('Original air date', $field->getName());
        self::assertNull($field->getDescription());
        self::assertTrue($field->isRequired());
        self::assertEquals(0, $field->getParameters()->getParameter1());
        self::assertEquals(7, $field->getParameters()->getParameter2());
        self::assertNull($field->getParameters()->getDefaultValue());

        $command = new UpdateDateFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Release date',
            'description'  => 'Date of the release',
            'required'     => false,
            'minValue'     => 1,
            'maxValue'     => 14,
            'defaultValue' => 10,
        ]);

        $this->commandbus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->find($field->getId());

        self::assertEquals(FieldType::DATE, $field->getType());
        self::assertEquals('Release date', $field->getName());
        self::assertEquals('Date of the release', $field->getDescription());
        self::assertFalse($field->isRequired());
        self::assertEquals(1, $field->getParameters()->getParameter1());
        self::assertEquals(14, $field->getParameters()->getParameter2());
        self::assertEquals(10, $field->getParameters()->getDefaultValue());
    }

    /**
     * @expectedException \eTraxis\CommandBus\Middleware\ValidationException
     * @expectedExceptionMessage Maximum value should be greater then minimum one.
     */
    public function testMinMaxValues()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Original air date']);

        $command = new UpdateDateFieldCommand([
            'id'       => $field->getId(),
            'name'     => $field->getName(),
            'required' => $field->isRequired(),
            'minValue' => 7,
            'maxValue' => 0,
        ]);

        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \eTraxis\CommandBus\Middleware\ValidationException
     * @expectedExceptionMessage Default value should be in range from 0 to 7.
     */
    public function testDefaultValue()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Original air date']);

        $command = new UpdateDateFieldCommand([
            'id'           => $field->getId(),
            'name'         => $field->getName(),
            'required'     => $field->isRequired(),
            'minValue'     => 0,
            'maxValue'     => 7,
            'defaultValue' => 10,
        ]);

        $this->commandbus->handle($command);
    }
}
