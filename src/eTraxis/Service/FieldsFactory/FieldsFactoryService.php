<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service\FieldsFactory;

use eTraxis\Entity\Field;
use eTraxis\SimpleBus\Fields;

/**
 * Factory for field commands.
 */
class FieldsFactoryService implements FieldsFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCreateCommand($type, $values = [])
    {
        switch ($type) {

            case Field::TYPE_NUMBER:
                return new Fields\CreateNumberFieldCommand($values);

            case Field::TYPE_STRING:
                return new Fields\CreateStringFieldCommand($values);

            case Field::TYPE_TEXT:
                return new Fields\CreateTextFieldCommand($values);

            case Field::TYPE_CHECKBOX:
                return new Fields\CreateCheckboxFieldCommand($values);

            case Field::TYPE_LIST:
                return new Fields\CreateListFieldCommand($values);

            case Field::TYPE_RECORD:
                return new Fields\CreateRecordFieldCommand($values);

            case Field::TYPE_DATE:
                return new Fields\CreateDateFieldCommand($values);

            case Field::TYPE_DURATION:
                return new Fields\CreateDurationFieldCommand($values);

            case Field::TYPE_DECIMAL:
                return new Fields\CreateDecimalFieldCommand($values);

            default:
                return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdateCommand($type, $values = [])
    {
        switch ($type) {

            case Field::TYPE_NUMBER:
                return new Fields\UpdateNumberFieldCommand($values);

            case Field::TYPE_STRING:
                return new Fields\UpdateStringFieldCommand($values);

            case Field::TYPE_TEXT:
                return new Fields\UpdateTextFieldCommand($values);

            case Field::TYPE_CHECKBOX:
                return new Fields\UpdateCheckboxFieldCommand($values);

            case Field::TYPE_LIST:
                return new Fields\UpdateListFieldCommand($values);

            case Field::TYPE_RECORD:
                return new Fields\UpdateRecordFieldCommand($values);

            case Field::TYPE_DATE:
                return new Fields\UpdateDateFieldCommand($values);

            case Field::TYPE_DURATION:
                return new Fields\UpdateDurationFieldCommand($values);

            case Field::TYPE_DECIMAL:
                return new Fields\UpdateDecimalFieldCommand($values);

            default:
                return null;
        }
    }
}
