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

/**
 * Factory for field commands.
 */
class FieldsCommandFactory
{
    /**
     * Returns command to create a field.
     *
     * @param   int   $type   Field type.
     * @param   array $values Initial values.
     *
     * @return  CreateFieldBaseCommand|null
     */
    public static function getCreateCommand($type, $values = [])
    {
        switch ($type) {

            case Field::TYPE_NUMBER:
                return new CreateNumberFieldCommand($values);

            case Field::TYPE_STRING:
                return new CreateStringFieldCommand($values);

            case Field::TYPE_TEXT:
                return new CreateTextFieldCommand($values);

            case Field::TYPE_CHECKBOX:
                return new CreateCheckboxFieldCommand($values);

            case Field::TYPE_LIST:
                return new CreateListFieldCommand($values);

            case Field::TYPE_ISSUE:
                return new CreateIssueFieldCommand($values);

            case Field::TYPE_DATE:
                return new CreateDateFieldCommand($values);

            case Field::TYPE_DURATION:
                return new CreateDurationFieldCommand($values);

            case Field::TYPE_DECIMAL:
                return new CreateDecimalFieldCommand($values);

            default:
                return null;
        }
    }

    /**
     * Returns command to update a field.
     *
     * @param   int   $type   Field type.
     * @param   array $values Initial values.
     *
     * @return  UpdateFieldBaseCommand|null
     */
    public static function getUpdateCommand($type, $values = [])
    {
        switch ($type) {

            case Field::TYPE_NUMBER:
                return new UpdateNumberFieldCommand($values);

            case Field::TYPE_STRING:
                return new UpdateStringFieldCommand($values);

            case Field::TYPE_TEXT:
                return new UpdateTextFieldCommand($values);

            case Field::TYPE_CHECKBOX:
                return new UpdateCheckboxFieldCommand($values);

            case Field::TYPE_LIST:
                return new UpdateListFieldCommand($values);

            case Field::TYPE_ISSUE:
                return new UpdateIssueFieldCommand($values);

            case Field::TYPE_DATE:
                return new UpdateDateFieldCommand($values);

            case Field::TYPE_DURATION:
                return new UpdateDurationFieldCommand($values);

            case Field::TYPE_DECIMAL:
                return new UpdateDecimalFieldCommand($values);

            default:
                return null;
        }
    }
}
