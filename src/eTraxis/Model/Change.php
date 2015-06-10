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


namespace eTraxis\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Issue change.
 *
 * @ORM\Table(name="tbl_changes",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_changes", columns={"event_id", "field_id"})
 *            })
 * @ORM\Entity
 */
class Change
{
    /**
     * @var int Unique ID.
     *
     * @ORM\Column(name="change_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int Changing event ID.
     *
     * @ORM\Column(name="event_id", type="integer")
     */
    private $eventId;

    /**
     * @var int Changed field ID.
     *
     * @ORM\Column(name="field_id", type="integer", nullable=true)
     */
    private $fieldId;

    /**
     * @var int Old value ID.
     *
     * @ORM\Column(name="old_value_id", type="integer", nullable=true)
     */
    private $oldValueId;

    /**
     * @var int New value ID.
     *
     * @ORM\Column(name="new_value_id", type="integer", nullable=true)
     */
    private $newValueId;

    /**
     * @var Event Changing event.
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="event_id")
     */
    private $event;

    /**
     * @var Field Changed field.
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * Standard setter.
     *
     * @param   int $eventId
     *
     * @return  self
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;

        return $this;
    }

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
     * @param   int $oldValueId
     *
     * @return  self
     */
    public function setOldValueId($oldValueId)
    {
        $this->oldValueId = $oldValueId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getOldValueId()
    {
        return $this->oldValueId;
    }

    /**
     * Standard setter.
     *
     * @param   int $newValueId
     *
     * @return  self
     */
    public function setNewValueId($newValueId)
    {
        $this->newValueId = $newValueId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getNewValueId()
    {
        return $this->newValueId;
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
