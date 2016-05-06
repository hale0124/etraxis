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
 * Number field.
 */
class NumberField extends AbstractField
{
    // Constraints.
    const MIN_VALUE = -1000000000;
    const MAX_VALUE = 1000000000;

    /**
     * {@inheritdoc}
     */
    protected function getSupportedKeys()
    {
        return ['minValue', 'maxValue', 'defaultValue'];
    }

    /**
     * Sets minimum allowed value of the field.
     *
     * @param   int $value
     *
     * @return  self
     */
    public function setMinValue(int $value)
    {
        if ($value < self::MIN_VALUE) {
            $value = self::MIN_VALUE;
        }

        if ($value > self::MAX_VALUE) {
            $value = self::MAX_VALUE;
        }

        $this->field->getParameters()->setParameter1($value);

        return $this;
    }

    /**
     * Returns minimum allowed value of the field.
     *
     * @return  int
     */
    public function getMinValue()
    {
        return $this->field->getParameters()->getParameter1();
    }

    /**
     * Sets maximum allowed value of the field.
     *
     * @param   int $value
     *
     * @return  self
     */
    public function setMaxValue(int $value)
    {
        if ($value < self::MIN_VALUE) {
            $value = self::MIN_VALUE;
        }

        if ($value > self::MAX_VALUE) {
            $value = self::MAX_VALUE;
        }

        $this->field->getParameters()->setParameter2($value);

        return $this;
    }

    /**
     * Returns maximum allowed value of the field.
     *
     * @return  int
     */
    public function getMaxValue()
    {
        return $this->field->getParameters()->getParameter2();
    }

    /**
     * Sets default value of the field.
     *
     * @param   int|null $value
     *
     * @return  self
     */
    public function setDefaultValue(int $value = null)
    {
        if ($value !== null) {

            if ($value < self::MIN_VALUE) {
                $value = self::MIN_VALUE;
            }

            if ($value > self::MAX_VALUE) {
                $value = self::MAX_VALUE;
            }
        }

        $this->field->getParameters()->setDefaultValue($value);

        return $this;
    }

    /**
     * Returns default value of the field.
     *
     * @return  int|null
     */
    public function getDefaultValue()
    {
        return $this->field->getParameters()->getDefaultValue();
    }
}
