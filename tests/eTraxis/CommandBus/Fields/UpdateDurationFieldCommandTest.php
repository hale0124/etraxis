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

class UpdateDurationFieldCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Running time']);

        self::assertEquals(FieldType::DURATION, $field->getType());
        self::assertEquals('Running time', $field->getName());
        self::assertNull($field->getDescription());
        self::assertTrue($field->isRequired());
        self::assertEquals(0, $field->getParameters()->getParameter1());
        self::assertEquals(1440, $field->getParameters()->getParameter2());
        self::assertNull($field->getParameters()->getDefaultValue());

        $command = new UpdateDurationFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Episode duration',
            'description'  => 'Running time',
            'required'     => false,
            'minValue'     => '0:01',
            'maxValue'     => '2:00',
            'defaultValue' => '0:22',
        ]);

        $this->commandbus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->find($field->getId());

        self::assertEquals(FieldType::DURATION, $field->getType());
        self::assertEquals('Episode duration', $field->getName());
        self::assertEquals('Running time', $field->getDescription());
        self::assertFalse($field->isRequired());
        self::assertEquals(1, $field->getParameters()->getParameter1());
        self::assertEquals(120, $field->getParameters()->getParameter2());
        self::assertEquals(22, $field->getParameters()->getDefaultValue());
    }

    /**
     * @expectedException \eTraxis\CommandBus\Middleware\ValidationException
     * @expectedExceptionMessage Maximum value should be greater then minimum one.
     */
    public function testMinMaxValues()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Running time']);

        $command = new UpdateDurationFieldCommand([
            'id'       => $field->getId(),
            'name'     => $field->getName(),
            'required' => $field->isRequired(),
            'minValue' => '24:00',
            'maxValue' => '0:00',
        ]);

        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \eTraxis\CommandBus\Middleware\ValidationException
     * @expectedExceptionMessage Default value should be in range from 0:00 to 23:59.
     */
    public function testDefaultValue()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Running time']);

        $command = new UpdateDurationFieldCommand([
            'id'           => $field->getId(),
            'name'         => $field->getName(),
            'required'     => $field->isRequired(),
            'minValue'     => '0:00',
            'maxValue'     => '23:59',
            'defaultValue' => '24:00',
        ]);

        $this->commandbus->handle($command);
    }
}
