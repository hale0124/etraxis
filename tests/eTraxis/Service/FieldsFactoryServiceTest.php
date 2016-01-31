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
use eTraxis\Tests\BaseTestCase;

class FieldsFactoryServiceTest extends BaseTestCase
{
    public function testCreate()
    {
        $factory = new FieldsFactoryService();

        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateNumberFieldCommand',   $factory->getCreateCommand(Field::TYPE_NUMBER));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateStringFieldCommand',   $factory->getCreateCommand(Field::TYPE_STRING));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateTextFieldCommand',     $factory->getCreateCommand(Field::TYPE_TEXT));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateCheckboxFieldCommand', $factory->getCreateCommand(Field::TYPE_CHECKBOX));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateListFieldCommand',     $factory->getCreateCommand(Field::TYPE_LIST));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateRecordFieldCommand',   $factory->getCreateCommand(Field::TYPE_RECORD));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateDateFieldCommand',     $factory->getCreateCommand(Field::TYPE_DATE));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateDurationFieldCommand', $factory->getCreateCommand(Field::TYPE_DURATION));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\CreateDecimalFieldCommand',  $factory->getCreateCommand(Field::TYPE_DECIMAL));

        $this->assertNull($factory->getCreateCommand(0));
    }

    public function testUpdate()
    {
        $factory = new FieldsFactoryService();

        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateNumberFieldCommand',   $factory->getUpdateCommand(Field::TYPE_NUMBER));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateStringFieldCommand',   $factory->getUpdateCommand(Field::TYPE_STRING));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateTextFieldCommand',     $factory->getUpdateCommand(Field::TYPE_TEXT));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateCheckboxFieldCommand', $factory->getUpdateCommand(Field::TYPE_CHECKBOX));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateListFieldCommand',     $factory->getUpdateCommand(Field::TYPE_LIST));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateRecordFieldCommand',   $factory->getUpdateCommand(Field::TYPE_RECORD));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateDateFieldCommand',     $factory->getUpdateCommand(Field::TYPE_DATE));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateDurationFieldCommand', $factory->getUpdateCommand(Field::TYPE_DURATION));
        $this->assertInstanceOf('\eTraxis\SimpleBus\Fields\UpdateDecimalFieldCommand',  $factory->getUpdateCommand(Field::TYPE_DECIMAL));

        $this->assertNull($factory->getUpdateCommand(0));
    }
}
