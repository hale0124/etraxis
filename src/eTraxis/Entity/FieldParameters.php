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
     * @ORM\Column(name="param1", type="integer", nullable=true)
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
     * @ORM\Column(name="param2", type="integer", nullable=true)
     */
    private $parameter2;

    /**
     * @var int Default value of the field. Depends on field type as following:
     *
     *          "number"   - default integer value (from -1000000000 till +1000000000)
     *          "decimal"  - default decimal value (foreign key to "decimal_values" table)
     *          "string"   - default string value (foreign key to "string_values" table)
     *          "text"     - default string value (foreign key to "text_values" table)
     *          "checkbox" - default state of checkbox (0 - unchecked, 1 - checked)
     *          "list"     - integer value of default list item (see "list_values" table)
     *          "record"   - NULL (not used)
     *          "date"     - default date value (amount of days since current date; negative value shifts to the past)
     *          "duration" - default duration value (amount of minutes from 0:00 till 999999:59)
     *
     * @ORM\Column(name="value_id", type="integer", nullable=true)
     */
    private $defaultValue;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->parameter1   = null;
        $this->parameter2   = null;
        $this->defaultValue = null;
    }

    /**
     * Standard setter.
     *
     * @param   int $parameter1
     *
     * @return  self
     */
    public function setParameter1($parameter1)
    {
        $this->parameter1 = $parameter1;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getParameter1()
    {
        return $this->parameter1;
    }

    /**
     * Standard setter.
     *
     * @param   int $parameter2
     *
     * @return  self
     */
    public function setParameter2($parameter2)
    {
        $this->parameter2 = $parameter2;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getParameter2()
    {
        return $this->parameter2;
    }

    /**
     * Standard setter.
     *
     * @param   int $defaultValue
     *
     * @return  self
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
}
