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
 * Attachment.
 *
 * @ORM\Table(name="tbl_attachments",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_attachments", columns={"event_id"})
 *            })
 * @ORM\Entity
 */
class Attachment
{
    // Constraints.
    const MAX_NAME = 100;
    const MAX_TYPE = 100;

    /**
     * @var int Unique ID.
     *
     * @ORM\Column(name="attachment_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int Event ID.
     *
     * @ORM\Column(name="event_id", type="integer")
     */
    private $eventId;

    /**
     * @var string Attachment name.
     *
     * @ORM\Column(name="attachment_name", type="string", length=100)
     */
    private $name;

    /**
     * @var string Attachment type.
     *
     * @ORM\Column(name="attachment_type", type="string", length=100)
     */
    private $type;

    /**
     * @var int Attachment size.
     *
     * @ORM\Column(name="attachment_size", type="integer")
     */
    private $size;

    /**
     * @var int Whether attachment is removed.
     *
     * @ORM\Column(name="is_removed", type="integer")
     */
    private $isRemoved;

    /**
     * @var Event Event.
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="event_id")
     */
    private $event;

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
     * @param   string $name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Standard setter.
     *
     * @param   string $type
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
     * @return  string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Standard setter.
     *
     * @param   int $size
     *
     * @return  self
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Standard setter.
     *
     * @param   bool $isRemoved
     *
     * @return  self
     */
    public function setRemoved($isRemoved)
    {
        $this->isRemoved = $isRemoved ? 1 : 0;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  bool
     */
    public function isRemoved()
    {
        return (bool) $this->isRemoved;
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
}
