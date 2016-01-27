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

class FieldsCommandFactoryTest extends BaseTestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateNumberFieldCommand',   FieldsCommandFactory::getCreateCommand(Field::TYPE_NUMBER));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateStringFieldCommand',   FieldsCommandFactory::getCreateCommand(Field::TYPE_STRING));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateTextFieldCommand',     FieldsCommandFactory::getCreateCommand(Field::TYPE_TEXT));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateCheckboxFieldCommand', FieldsCommandFactory::getCreateCommand(Field::TYPE_CHECKBOX));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateListFieldCommand',     FieldsCommandFactory::getCreateCommand(Field::TYPE_LIST));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateRecordFieldCommand',   FieldsCommandFactory::getCreateCommand(Field::TYPE_RECORD));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateDateFieldCommand',     FieldsCommandFactory::getCreateCommand(Field::TYPE_DATE));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateDurationFieldCommand', FieldsCommandFactory::getCreateCommand(Field::TYPE_DURATION));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateDecimalFieldCommand',  FieldsCommandFactory::getCreateCommand(Field::TYPE_DECIMAL));

        $this->assertNull(FieldsCommandFactory::getCreateCommand(0));
    }

    public function testUpdate()
    {
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateNumberFieldCommand',   FieldsCommandFactory::getUpdateCommand(Field::TYPE_NUMBER));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateStringFieldCommand',   FieldsCommandFactory::getUpdateCommand(Field::TYPE_STRING));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateTextFieldCommand',     FieldsCommandFactory::getUpdateCommand(Field::TYPE_TEXT));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateCheckboxFieldCommand', FieldsCommandFactory::getUpdateCommand(Field::TYPE_CHECKBOX));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateListFieldCommand',     FieldsCommandFactory::getUpdateCommand(Field::TYPE_LIST));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateRecordFieldCommand',   FieldsCommandFactory::getUpdateCommand(Field::TYPE_RECORD));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateDateFieldCommand',     FieldsCommandFactory::getUpdateCommand(Field::TYPE_DATE));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateDurationFieldCommand', FieldsCommandFactory::getUpdateCommand(Field::TYPE_DURATION));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateDecimalFieldCommand',  FieldsCommandFactory::getUpdateCommand(Field::TYPE_DECIMAL));

        $this->assertNull(FieldsCommandFactory::getUpdateCommand(0));
    }
}
