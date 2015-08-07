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
 * Field value.
 *
 * @ORM\Table(name="tbl_field_values",
 *            indexes={
 *                @ORM\Index(name="ix_value",     columns={"value_id"}),
 *                @ORM\Index(name="ix_fva_comb",  columns={"value_id", "field_type", "is_latest", "event_id"}),
 *                @ORM\Index(name="ix_fva_comb2", columns={"field_id", "value_id", "is_latest", "event_id"})
 *            })
 * @ORM\Entity
 */
class FieldValue
{
    /**
     * @var int Event ID.
     *
     * @ORM\Column(name="event_id", type="integer")
     * @ORM\Id
     */
    private $eventId;

    /**
     * @var int Field ID.
     *
     * @ORM\Column(name="field_id", type="integer")
     * @ORM\Id
     */
    private $fieldId;

    /**
     * @var int Type of the field (obsolete).
     *
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
     *          "issue"    - issue ID
     *          "date"     - date value (Unix Epoch timestamp)
     *          "duration" - duration value (amount of minutes from 0:00 till 999999:59)
     *
     * @ORM\Column(name="value_id", type="integer", nullable=true)
     */
    private $valueId;

    /**
     * @var bool Whether this value is current one for this field of the issue.
     *
     * @ORM\Column(name="is_latest", type="integer")
     */
    private $isCurrent;

    /**
     * @var Event Event.
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="event_id")
     */
    private $event;

    /**
     * @var Field Field.
     *
     * @ORM\ManyToOne(targetEntity="Field")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="field_id")
     */
    private $field;

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getEventId()
    {
        return $this->eventId;
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
     * @param   int $type
     *
     * @return  self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Standard setter.
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
     * Standard getter.
     *
     * @return  int
     */
    public function getValueId()
    {
        return $this->valueId;
    }

    /**
     * Standard setter.
     *
     * @param   bool $isCurrent
     *
     * @return  self
     */
    public function setCurrent($isCurrent)
    {
        $this->isCurrent = (bool) $isCurrent;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  bool
     */
    public function isCurrent()
    {
        return (bool) $this->isCurrent;
    }

    /**
     * Standard setter.
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
     * Standard getter.
     *
     * @return  Event
     */
    public function getEvent()
    {
        return $this->event;
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
