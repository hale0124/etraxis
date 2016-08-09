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

use eTraxis\Constant\Seconds;

/**
 * Duration field.
 */
class DurationField extends AbstractField
{
    // Constraints.
    const MIN_VALUE = 0;
    const MAX_VALUE = 59999999;

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
     * @param   string $value
     *
     * @return  self
     */
    public function setMinValue(string $value)
    {
        $duration = self::str2int($value);

        if ($duration < self::MIN_VALUE) {
            $duration = self::MIN_VALUE;
        }

        if ($duration > self::MAX_VALUE) {
            $duration = self::MAX_VALUE;
        }

        $this->field->getParameters()->setParameter1($duration);

        return $this;
    }

    /**
     * Returns minimum allowed value of the field.
     *
     * @return  string
     */
    public function getMinValue()
    {
        return self::int2str($this->field->getParameters()->getParameter1());
    }

    /**
     * Sets maximum allowed value of the field.
     *
     * @param   string $value
     *
     * @return  self
     */
    public function setMaxValue(string $value)
    {
        $duration = self::str2int($value);

        if ($duration < self::MIN_VALUE) {
            $duration = self::MIN_VALUE;
        }

        if ($duration > self::MAX_VALUE) {
            $duration = self::MAX_VALUE;
        }

        $this->field->getParameters()->setParameter2($duration);

        return $this;
    }

    /**
     * Returns maximum allowed value of the field.
     *
     * @return  string
     */
    public function getMaxValue()
    {
        return self::int2str($this->field->getParameters()->getParameter2());
    }

    /**
     * Sets default value of the field.
     *
     * @param   string|null $value
     *
     * @return  self
     */
    public function setDefaultValue(string $value = null)
    {
        $this->field->getParameters()->setDefaultValue(self::str2int($value));

        return $this;
    }

    /**
     * Returns default value of the field.
     *
     * @return  string|null
     */
    public function getDefaultValue()
    {
        return self::int2str($this->field->getParameters()->getDefaultValue());
    }

    /**
     * Converts specified number of minutes to its string representation in format "hh:mm".
     *
     * @param   int|null $value Number of minutes.
     *
     * @return  string|null String representation (e.g. for 127 it will be "2:07").
     */
    public static function int2str(int $value = null)
    {
        if ($value === null) {
            return null;
        }

        if ($value < self::MIN_VALUE) {
            $value = self::MIN_VALUE;
        }

        if ($value > self::MAX_VALUE) {
            $value = self::MAX_VALUE;
        }

        return intdiv($value, Seconds::ONE_MINUTE) . ':' . str_pad($value % Seconds::ONE_MINUTE, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Converts specified string representation of amount of minutes to the integer number.
     *
     * @param   string|null $value String representation.
     *
     * @return  int|null Number of minutes (e.g. 127 for "2:07").
     */
    public static function str2int(string $value = null)
    {
        if ($value === null) {
            return null;
        }

        /** @noinspection NotOptimalRegularExpressionsInspection */
        if (!preg_match('/^\d{1,6}:[0-5][0-9]$/', $value)) {
            return null;
        }

        list($hh, $mm) = explode(':', $value);

        return $hh * Seconds::ONE_MINUTE + $mm;
    }
}
