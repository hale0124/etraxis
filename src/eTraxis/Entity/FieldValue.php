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
 * Field value.
 *
 * @ORM\Table(name="field_values")
 * @ORM\Entity
 */
class FieldValue
{
    /**
     * @var Event Event.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $event;

    /**
     * @var Field Field.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Field")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id")
     */
    private $field;

    /**
     * @var bool Whether this value is current one for this field of the record.
     *
     * @ORM\Column(name="is_current", type="boolean")
     */
    private $isCurrent;

    /**
     * @var int Value of the field. Depends on field type as following:
     *
     *      NUMBER   - integer value (from -1000000000 till +1000000000)
     *      DECIMAL  - decimal value (foreign key to "DecimalValue" entity)
     *      STRING   - string value (foreign key to "StringValue" entity)
     *      TEXT     - string value (foreign key to "TextValue" entity)
     *      CHECKBOX - state of checkbox (0 - unchecked, 1 - checked)
     *      LIST     - integer value of list item (see "ListItem" entity)
     *      RECORD   - record ID
     *      DATE     - date value (Unix Epoch timestamp)
     *      DURATION - duration value (amount of minutes from 0:00 till 999999:59)
     *
     * @ORM\Column(name="value", type="integer", nullable=true)
     */
    private $value;
}
