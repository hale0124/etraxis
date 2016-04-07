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
 * Additional data for filtering by values in custom fields.
 *
 * @ORM\Table(name="tbl_filter_fields")
 * @ORM\Entity
 */
class FilterField
{
    /**
     * @var Filter Filter.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Filter")
     * @ORM\JoinColumn(name="filter_id", referencedColumnName="filter_id", onDelete="CASCADE")
     */
    private $filter;

    /**
     * @var Field Field which values should be examined.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Field")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="field_id", onDelete="CASCADE")
     */
    private $field;

    /**
     * @var int Allowed range of the field values. Depends on the field type as following:
     *
     *          "number"   - minimum integer value (from -1000000000 till +1000000000), if NULL then any
     *          "decimal"  - minimum decimal value (foreign key to "DecimalValue"), if NULL then any
     *          "string"   - substring of value (foreign key to "StringValue"), if NULL then only records with empty fields will be shown
     *          "text"     - substring of value (foreign key to "StringValue"), if NULL then only records with empty fields will be shown
     *          "checkbox" - state of checkbox (0 — unchecked, 1 — checked)
     *          "list"     - integer value of list item, if NULL then any
     *          "record"   - record ID, if NULL then any
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
     *          "record"   - NULL (not used)
     *          "date"     - maximum date value (Unix Epoch timestamp), if NULL then any
     *          "duration" - maximum duration value (amount of minutes from 0:00 till 999999:59), if NULL then any
     *
     * @ORM\Column(name="param2", type="integer", nullable=true)
     */
    private $parameter2;
}
