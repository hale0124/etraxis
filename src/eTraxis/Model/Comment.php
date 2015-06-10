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
 * Comment.
 *
 * @ORM\Table(name="tbl_comments",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_comments", columns={"event_id"})
 *            })
 * @ORM\Entity
 */
class Comment
{
    /**
     * @var int Unique ID.
     *
     * @ORM\Column(name="comment_id", type="integer")
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
     * @var bool Whether comment is confidential.
     *
     * @ORM\Column(name="is_confidential", type="integer")
     */
    private $isConfidential;

    /**
     * @var string Comment body.
     *
     * @ORM\Column(name="comment_body", type="text")
     */
    private $comment;

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
     * @param   bool $isConfidential
     *
     * @return  self
     */
    public function setConfidential($isConfidential)
    {
        $this->isConfidential = $isConfidential;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  bool
     */
    public function isConfidential()
    {
        return (boolean) $this->isConfidential;
    }

    /**
     * Standard setter.
     *
     * @param   string $comment
     *
     * @return  self
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getComment()
    {
        return $this->comment;
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
