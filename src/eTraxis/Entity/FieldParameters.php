<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Field embedded parameters.
 *
 * @ORM\Embeddable
 */
class FieldParameters
{
    /**
     * @var int First parameter of the field. Depends on field type as following:
     *
     *          "number"   - minimum of range of allowed values (from -1000000000 till +1000000000)
     *          "decimal"  - minimum of range of allowed values (foreign key to "decimal_values" table)
     *          "string"   - maximum allowed length of values (up to 250)
     *          "text"     - maximum allowed length of values (up to 4000)
     *          "checkbox" - NULL (not used)
     *          "list"     - NULL (not used)
     *          "record"   - NULL (not used)
     *          "date"     - minimum of range of allowed values (amount of days since current date; negative value shifts to the past)
     *          "duration" - minimum of range of allowed values (amount of minutes from 0:00 till 999999:59)
     *
     * @ORM\Column(name="parameter1", type="integer", nullable=true)
     */
    private $parameter1;

    /**
     * @var int Second parameter of the field. Depends on field type as following:
     *
     *          "number"   - maximum of range of allowed values (from -1000000000 till +1000000000)
     *          "decimal"  - maximum of range of allowed values (foreign key to "decimal_values" table)
     *          "string"   - NULL (not used)
     *          "text"     - NULL (not used)
     *          "checkbox" - NULL (not used)
     *          "list"     - NULL (not used)
     *          "record"   - NULL (not used)
     *          "date"     - maximum of range of allowed values (amount of days since current date; negative value shifts to the past)
     *          "duration" - maximum of range of allowed values (amount of minutes from 0:00 till 999999:59)
     *
     * @ORM\Column(name="parameter2", type="integer", nullable=true)
     */
    private $parameter2;

    /**
     * @var int Default value of the field. Depends on field type as following:
     *
     *          "number"   - see "FieldValue::$value" for explanation
     *          "decimal"  - see "FieldValue::$value" for explanation
     *          "string"   - see "FieldValue::$value" for explanation
     *          "text"     - see "FieldValue::$value" for explanation
     *          "checkbox" - see "FieldValue::$value" for explanation
     *          "list"     - see "FieldValue::$value" for explanation
     *          "record"   - NULL (not used)
     *          "date"     - default date value (amount of days since current date; negative value shifts to the past)
     *          "duration" - see "FieldValue::$value" for explanation
     *
     * @ORM\Column(name="default_value", type="integer", nullable=true)
     */
    private $defaultValue;

    /**
     * Property setter.
     *
     * @param   int|null $parameter1
     *
     * @return  self
     */
    public function setParameter1(int $parameter1 = null)
    {
        $this->parameter1 = $parameter1;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int|null
     */
    public function getParameter1()
    {
        return $this->parameter1;
    }

    /**
     * Property setter.
     *
     * @param   int|null $parameter2
     *
     * @return  self
     */
    public function setParameter2(int $parameter2 = null)
    {
        $this->parameter2 = $parameter2;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int|null
     */
    public function getParameter2()
    {
        return $this->parameter2;
    }

    /**
     * Property setter.
     *
     * @param   int|null $defaultValue
     *
     * @return  self
     */
    public function setDefaultValue(int $defaultValue = null)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int|null
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
}
