<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service;

use eTraxis\Entity\Field;
use eTraxis\Service\FieldsFactory\FieldsFactoryService;
use eTraxis\SimpleBus\Fields;
use eTraxis\Tests\BaseTestCase;

class FieldsFactoryServiceTest extends BaseTestCase
{
    public function testCreate()
    {
        $factory = new FieldsFactoryService();

        self::assertInstanceOf(Fields\CreateNumberFieldCommand::class,   $factory->getCreateCommand(Field::TYPE_NUMBER));
        self::assertInstanceOf(Fields\CreateStringFieldCommand::class,   $factory->getCreateCommand(Field::TYPE_STRING));
        self::assertInstanceOf(Fields\CreateTextFieldCommand::class,     $factory->getCreateCommand(Field::TYPE_TEXT));
        self::assertInstanceOf(Fields\CreateCheckboxFieldCommand::class, $factory->getCreateCommand(Field::TYPE_CHECKBOX));
        self::assertInstanceOf(Fields\CreateListFieldCommand::class,     $factory->getCreateCommand(Field::TYPE_LIST));
        self::assertInstanceOf(Fields\CreateRecordFieldCommand::class,   $factory->getCreateCommand(Field::TYPE_RECORD));
        self::assertInstanceOf(Fields\CreateDateFieldCommand::class,     $factory->getCreateCommand(Field::TYPE_DATE));
        self::assertInstanceOf(Fields\CreateDurationFieldCommand::class, $factory->getCreateCommand(Field::TYPE_DURATION));
        self::assertInstanceOf(Fields\CreateDecimalFieldCommand::class,  $factory->getCreateCommand(Field::TYPE_DECIMAL));

        self::assertNull($factory->getCreateCommand(0));
    }

    public function testUpdate()
    {
        $factory = new FieldsFactoryService();

        self::assertInstanceOf(Fields\UpdateNumberFieldCommand::class,   $factory->getUpdateCommand(Field::TYPE_NUMBER));
        self::assertInstanceOf(Fields\UpdateStringFieldCommand::class,   $factory->getUpdateCommand(Field::TYPE_STRING));
        self::assertInstanceOf(Fields\UpdateTextFieldCommand::class,     $factory->getUpdateCommand(Field::TYPE_TEXT));
        self::assertInstanceOf(Fields\UpdateCheckboxFieldCommand::class, $factory->getUpdateCommand(Field::TYPE_CHECKBOX));
        self::assertInstanceOf(Fields\UpdateListFieldCommand::class,     $factory->getUpdateCommand(Field::TYPE_LIST));
        self::assertInstanceOf(Fields\UpdateRecordFieldCommand::class,   $factory->getUpdateCommand(Field::TYPE_RECORD));
        self::assertInstanceOf(Fields\UpdateDateFieldCommand::class,     $factory->getUpdateCommand(Field::TYPE_DATE));
        self::assertInstanceOf(Fields\UpdateDurationFieldCommand::class, $factory->getUpdateCommand(Field::TYPE_DURATION));
        self::assertInstanceOf(Fields\UpdateDecimalFieldCommand::class,  $factory->getUpdateCommand(Field::TYPE_DECIMAL));

        self::assertNull($factory->getUpdateCommand(0));
    }
}
