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

        $this->assertInstanceOf(Fields\CreateNumberFieldCommand::class,   $factory->getCreateCommand(Field::TYPE_NUMBER));
        $this->assertInstanceOf(Fields\CreateStringFieldCommand::class,   $factory->getCreateCommand(Field::TYPE_STRING));
        $this->assertInstanceOf(Fields\CreateTextFieldCommand::class,     $factory->getCreateCommand(Field::TYPE_TEXT));
        $this->assertInstanceOf(Fields\CreateCheckboxFieldCommand::class, $factory->getCreateCommand(Field::TYPE_CHECKBOX));
        $this->assertInstanceOf(Fields\CreateListFieldCommand::class,     $factory->getCreateCommand(Field::TYPE_LIST));
        $this->assertInstanceOf(Fields\CreateRecordFieldCommand::class,   $factory->getCreateCommand(Field::TYPE_RECORD));
        $this->assertInstanceOf(Fields\CreateDateFieldCommand::class,     $factory->getCreateCommand(Field::TYPE_DATE));
        $this->assertInstanceOf(Fields\CreateDurationFieldCommand::class, $factory->getCreateCommand(Field::TYPE_DURATION));
        $this->assertInstanceOf(Fields\CreateDecimalFieldCommand::class,  $factory->getCreateCommand(Field::TYPE_DECIMAL));

        $this->assertNull($factory->getCreateCommand(0));
    }

    public function testUpdate()
    {
        $factory = new FieldsFactoryService();

        $this->assertInstanceOf(Fields\UpdateNumberFieldCommand::class,   $factory->getUpdateCommand(Field::TYPE_NUMBER));
        $this->assertInstanceOf(Fields\UpdateStringFieldCommand::class,   $factory->getUpdateCommand(Field::TYPE_STRING));
        $this->assertInstanceOf(Fields\UpdateTextFieldCommand::class,     $factory->getUpdateCommand(Field::TYPE_TEXT));
        $this->assertInstanceOf(Fields\UpdateCheckboxFieldCommand::class, $factory->getUpdateCommand(Field::TYPE_CHECKBOX));
        $this->assertInstanceOf(Fields\UpdateListFieldCommand::class,     $factory->getUpdateCommand(Field::TYPE_LIST));
        $this->assertInstanceOf(Fields\UpdateRecordFieldCommand::class,   $factory->getUpdateCommand(Field::TYPE_RECORD));
        $this->assertInstanceOf(Fields\UpdateDateFieldCommand::class,     $factory->getUpdateCommand(Field::TYPE_DATE));
        $this->assertInstanceOf(Fields\UpdateDurationFieldCommand::class, $factory->getUpdateCommand(Field::TYPE_DURATION));
        $this->assertInstanceOf(Fields\UpdateDecimalFieldCommand::class,  $factory->getUpdateCommand(Field::TYPE_DECIMAL));

        $this->assertNull($factory->getUpdateCommand(0));
    }
}
