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
 * @ORM\Table(name="tbl_field_values",
 *            indexes={
 *                @ORM\Index(name="ix_value",     columns={"value_id"}),
 *                @ORM\Index(name="ix_fva_comb",  columns={"value_id", "field_type", "is_latest", "event_id"}),
 *                @ORM\Index(name="ix_fva_comb2", columns={"field_id", "value_id", "is_latest", "event_id"})
 *            })
 * @ORM\Entity(repositoryClass="eTraxis\Repository\FieldValuesRepository")
 */
class FieldValue
{
    /**
     * @var Event Event.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="event_id", onDelete="CASCADE")
     */
    private $event;

    /**
     * @var Field Field.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Field")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="field_id")
     */
    private $field;

    /**
     * @deprecated 4.1.0
     * @ORM\Column(name="field_type", type="integer")
     */
    private $type;

    /**
     * @var int Value of the field. Depends on field type as following:
     *
     *          "number"   - integer value (from -1000000000 till +1000000000)
     *          "decimal"  - decimal value (foreign key to "decimal_values" table)
     *          "string"   - string value (foreign key to "string_values" table)
     *          "text"     - string value (foreign key to "text_values" table)
     *          "checkbox" - state of checkbox (0 - unchecked, 1 - checked)
     *          "list"     - integer value of list item (see "list_values" table)
     *          "record"   - record ID
     *          "date"     - date value (Unix Epoch timestamp)
     *          "duration" - duration value (amount of minutes from 0:00 till 999999:59)
     *
     * @ORM\Column(name="value_id", type="integer", nullable=true)
     */
    private $valueId;

    /**
     * @var int Whether this value is current one for this field of the record.
     *
     * @ORM\Column(name="is_latest", type="integer")
     */
    private $isCurrent;

    /**
     * Property setter.
     *
     * @param   Event $event
     *
     * @return  self
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Property setter.
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
     * Property getter.
     *
     * @return  Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Property setter.
     *
     * @param   string $type
     *
     * @return  self
     */
    public function setType($type)
    {
        /**
         * @deprecated 4.1.0 A stub for compatibility btw 3.6 and 4.0.
         */
        $types = [
            'number'   => 1,
            'string'   => 2,
            'text'     => 3,
            'checkbox' => 4,
            'list'     => 5,
            'record'   => 6,
            'date'     => 7,
            'duration' => 8,
            'decimal'  => 9,
        ];

        if (array_key_exists($type, $types)) {
            $this->type = $types[$type];
        }

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getType()
    {
        /**
         * @deprecated 4.1.0 A stub for compatibility btw 3.6 and 4.0.
         */
        $types = [
            1 => 'number',
            2 => 'string',
            3 => 'text',
            4 => 'checkbox',
            5 => 'list',
            6 => 'record',
            7 => 'date',
            8 => 'duration',
            9 => 'decimal',
        ];

        return $types[$this->type];
    }

    /**
     * Property setter.
     *
     * @param   int $valueId
     *
     * @return  self
     */
    public function setValueId($valueId)
    {
        $this->valueId = $valueId;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getValueId()
    {
        return $this->valueId;
    }

    /**
     * Property setter.
     *
     * @param   bool $isCurrent
     *
     * @return  self
     */
    public function setCurrent($isCurrent)
    {
        $this->isCurrent = $isCurrent ? 1 : 0;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  bool
     */
    public function isCurrent()
    {
        return (bool) $this->isCurrent;
    }
}
