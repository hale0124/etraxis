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

/**
 * Checkbox field.
 */
class CheckboxField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedKeys()
    {
        return ['defaultValue'];
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
        $this->field->getParameters()->setDefaultValue($value ? 1 : 0);

        return $this;
    }

    /**
     * Returns default value of the field.
     *
     * @return  bool
     */
    public function getDefaultValue()
    {
        return $this->field->getParameters()->getDefaultValue() ? true : false;
    }
}
