<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Additional data for filtering by values in custom fields.
 *
 * @ORM\Table(name="tbl_filter_fields")
 * @ORM\Entity
 */
class FilterField
{
    /**
     * @var int Filter ID.
     *
     * @ORM\Column(name="filter_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $filterId;

    /**
     * @var int Field ID which values should be examined.
     *
     * @ORM\Column(name="field_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $fieldId;

    /**
     * @var int Allowed range of the field values. Depends on the field type as following:
     *
     *          "number"   - minimum integer value (from -1000000000 till +1000000000), if NULL then any
     *          "decimal"  - minimum decimal value (foreign key to "DecimalValue"), if NULL then any
     *          "string"   - substring of value (foreign key to "StringValue"), if NULL then only issues with empty fields will be shown
     *          "text"     - substring of value (foreign key to "StringValue"), if NULL then only issues with empty fields will be shown
     *          "checkbox" - state of checkbox (0 — unchecked, 1 — checked)
     *          "list"     - integer value of list item, if NULL then any
     *          "issue"    - issue ID, if NULL then any
     *          "date"     - minimum date value (Unix Epoch timestamp), if NULL then any
     *          "duration" - minimum duration value (amount of minutes from 0:00 till 999999:59), if NULL then any
     *
     * @ORM\Column(name="param1", type="integer", nullable=true)
     */
    private $parameter1;

    /**
     * @var int Allowed range of the field values. Depends on the field type as following:
     *
     *          "number"   - maximum integer value (from -1000000000 till +1000000000), if NULL then any
     *          "decimal"  - maximum decimal value (foreign key to "DecimalValue"), if NULL then any
     *          "string"   - NULL (not used)
     *          "text"     - NULL (not used)
     *          "checkbox" - NULL (not used)
     *          "list"     - NULL (not used)
     *          "issue"    - NULL (not used)
     *          "date"     - maximum date value (Unix Epoch timestamp), if NULL then any
     *          "duration" - maximum duration value (amount of minutes from 0:00 till 999999:59), if NULL then any
     *
     * @ORM\Column(name="param2", type="integer", nullable=true)
     */
    private $parameter2;

    /**
     * @var Filter Filter.
     *
     * @ORM\ManyToOne(targetEntity="Filter")
     * @ORM\JoinColumn(name="filter_id", referencedColumnName="filter_id")
     */
    private $filter;

    /**
     * @var Field Field which values should be examined.
     *
     * @ORM\ManyToOne(targetEntity="Field")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="field_id")
     */
    private $field;

    /**
     * Standard setter.
     *
     * @param   int $filterId
     *
     * @return  self
     */
    public function setFilterId($filterId)
    {
        $this->filterId = $filterId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getFilterId()
    {
        return $this->filterId;
    }

    /**
     * Standard setter.
     *
     * @param   int $fieldId
     *
     * @return  self
     */
    public function setFieldId($fieldId)
    {
        $this->fieldId = $fieldId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getFieldId()
    {
        return $this->fieldId;
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
     * @param   Filter $filter
     *
     * @return  self
     */
    public function setFilter(Filter $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  Filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Standard setter.
     *
     * @param   Field $field
     *
     * @return  self
     */
    public function setField(Field $field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  Field
     */
    public function getField()
    {
        return $this->field;
    }
}
