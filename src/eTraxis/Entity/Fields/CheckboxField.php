<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Fields;

use eTraxis\Entity\Field;

/**
 * Checkbox field.
 */
class CheckboxField
{
    // Properties.
    protected $field;

    /**
     * Constructor.
     *
     * @param   Field $field
     */
    public function __construct(Field $field)
    {
        $this->field = $field;
    }

    /**
     * Sets default value of the field.
     *
     * @param   bool $value
     *
     * @return  self
     */
    public function setDefaultValue($value)
    {
        $this->field->setDefaultValue($value ? 1 : 0);

        return $this;
    }

    /**
     * Returns default value of the field.
     *
     * @return  bool
     */
    public function getDefaultValue()
    {
        return $this->field->getDefaultValue() ? true : false;
    }
}
