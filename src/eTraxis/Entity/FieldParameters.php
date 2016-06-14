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
     *      NUMBER   - minimum of range of allowed values (from -1000000000 till +1000000000)
     *      DECIMAL  - minimum of range of allowed values (foreign key to "decimal_values" table)
     *      STRING   - maximum allowed length of values (up to 250)
     *      TEXT     - maximum allowed length of values (up to 4000)
     *      CHECKBOX - NULL (not used)
     *      LIST     - NULL (not used)
     *      RECORD   - NULL (not used)
     *      DATE     - minimum of range of allowed values (amount of days since current date; negative value shifts to the past)
     *      DURATION - minimum of range of allowed values (amount of minutes from 0:00 till 999999:59)
     *
     * @ORM\Column(name="parameter1", type="integer", nullable=true)
     */
    private $parameter1;

    /**
     * @var int Second parameter of the field. Depends on field type as following:
     *
     *      NUMBER   - maximum of range of allowed values (from -1000000000 till +1000000000)
     *      DECIMAL  - maximum of range of allowed values (foreign key to "decimal_values" table)
     *      STRING   - NULL (not used)
     *      TEXT     - NULL (not used)
     *      CHECKBOX - NULL (not used)
     *      LIST     - NULL (not used)
     *      RECORD   - NULL (not used)
     *      DATE     - maximum of range of allowed values (amount of days since current date; negative value shifts to the past)
     *      DURATION - maximum of range of allowed values (amount of minutes from 0:00 till 999999:59)
     *
     * @ORM\Column(name="parameter2", type="integer", nullable=true)
     */
    private $parameter2;

    /**
     * @var int Default value of the field. Depends on field type as following:
     *
     *      NUMBER   - see "FieldValue::$value" for explanation
     *      DECIMAL  - see "FieldValue::$value" for explanation
     *      STRING   - see "FieldValue::$value" for explanation
     *      TEXT     - see "FieldValue::$value" for explanation
     *      CHECKBOX - see "FieldValue::$value" for explanation
     *      LIST     - see "FieldValue::$value" for explanation
     *      RECORD   - NULL (not used)
     *      DATE     - default date value (amount of days since current date; negative value shifts to the past)
     *      DURATION - see "FieldValue::$value" for explanation
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
