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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Record.
 *
 * @ORM\Table(name="tbl_records",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_records", columns={"creator_id", "creation_time"})
 *            })
 * @ORM\Entity(repositoryClass="eTraxis\Repository\RecordsRepository")
 */
class Record
{
    // Constraints.
    const MAX_SUBJECT = 250;

    /**
     * @var int Unique ID.
     *
     * @ORM\Column(name="record_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string Subject of the record.
     *
     * @ORM\Column(name="subject", type="string", length=250)
     */
    private $subject;

    /**
     * @var int ID of the current state.
     *
     * @ORM\Column(name="state_id", type="integer")
     */
    private $stateId;

    /**
     * @var int ID of the record's author.
     *
     * @ORM\Column(name="creator_id", type="integer")
     */
    private $authorId;

    /**
     * @var int ID of the current record's responsible.
     *
     * @ORM\Column(name="responsible_id", type="integer", nullable=true)
     */
    private $responsibleId;

    /**
     * @var int Unix Epoch timestamp when the record was created.
     *
     * @ORM\Column(name="creation_time", type="integer")
     */
    private $createdAt;

    /**
     * @var int Unix Epoch timestamp when the record was changed last time.
     *
     * @ORM\Column(name="change_time", type="integer")
     */
    private $changedAt;

    /**
     * @var int Unix Epoch timestamp when the record was closed.
     *
     * @ORM\Column(name="closure_time", type="integer", nullable=true)
     */
    private $closedAt;

    /**
     * @var int Unix Epoch timestamp when the postponed record will be resumed back.
     *
     * @ORM\Column(name="postpone_time", type="integer")
     */
    private $resumedAt;

    /**
     * @var State Current state of the record.
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="state_id")
     */
    private $state;

    /**
     * @var User Author of the the record.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="account_id")
     */
    private $author;

    /**
     * @var User Current responsible of the record.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="responsible_id", referencedColumnName="account_id")
     */
    private $responsible;

    /**
     * @var ArrayCollection List of record events.
     *
     * @ORM\OneToMany(targetEntity="Event", mappedBy="record")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
    private $history;

    /**
     * @var ArrayCollection List of state fields.
     *
     * @ORM\OneToMany(targetEntity="Watcher", mappedBy="record")
     */
    private $watchers;

    /**
     * @var ArrayCollection List of subrecords.
     *
     * @ORM\OneToMany(targetEntity="Child", mappedBy="parent")
     * @ORM\OrderBy({"childId" = "ASC"})
     */
    private $children;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->history  = new ArrayCollection();
        $this->watchers = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

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
     * @param   string $subject
     *
     * @return  self
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Standard setter.
     *
     * @param   int $stateId
     *
     * @return  self
     */
    public function setStateId($stateId)
    {
        $this->stateId = $stateId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * Standard setter.
     *
     * @param   int $authorId
     *
     * @return  self
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * Standard setter.
     *
     * @param   int $responsibleId
     *
     * @return  self
     */
    public function setResponsibleId($responsibleId)
    {
        $this->responsibleId = $responsibleId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getResponsibleId()
    {
        return $this->responsibleId;
    }

    /**
     * Standard setter.
     *
     * @param   int $createdAt
     *
     * @return  self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Standard setter.
     *
     * @param   int $changedAt
     *
     * @return  self
     */
    public function setChangedAt($changedAt)
    {
        $this->changedAt = $changedAt;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getChangedAt()
    {
        return $this->changedAt;
    }

    /**
     * Standard setter.
     *
     * @param   int $closedAt
     *
     * @return  self
     */
    public function setClosedAt($closedAt)
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getClosedAt()
    {
        return $this->closedAt;
    }

    /**
     * Standard setter.
     *
     * @param   int $resumedAt
     *
     * @return  self
     */
    public function setResumedAt($resumedAt)
    {
        $this->resumedAt = $resumedAt;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getResumedAt()
    {
        return $this->resumedAt;
    }

    /**
     * Standard setter.
     *
     * @param   State $state
     *
     * @return  self
     */
    public function setState(State $state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Standard setter.
     *
     * @param   User $author
     *
     * @return  self
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Standard setter.
     *
     * @param   User $responsible
     *
     * @return  self
     */
    public function setResponsible(User $responsible = null)
    {
        $this->responsible = $responsible;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  User
     */
    public function getResponsible()
    {
        return $this->responsible;
    }

    /**
     * Add event to the record.
     *
     * @param   Event $event
     *
     * @return  self
     */
    public function addEvent(Event $event)
    {
        $this->history[] = $event;

        return $this;
    }

    /**
     * Remove event from the record.
     *
     * @param   Event $event
     *
     * @return  self
     */
    public function removeEvent(Event $event)
    {
        $this->history->removeElement($event);

        return $this;
    }

    /**
     * Get record history.
     *
     * @return  ArrayCollection|Event[]
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Add watcher.
     *
     * @param   Watcher $watcher
     *
     * @return  self
     */
    public function addWatcher(Watcher $watcher)
    {
        $this->watchers[] = $watcher;

        return $this;
    }

    /**
     * Remove watcher.
     *
     * @param   Watcher $watcher
     *
     * @return  self
     */
    public function removeWatcher(Watcher $watcher)
    {
        $this->watchers->removeElement($watcher);

        return $this;
    }

    /**
     * Get list of record watchers.
     *
     * @return  ArrayCollection|Watcher[]
     */
    public function getWatchers()
    {
        return $this->watchers;
    }

    /**
     * Add subrecord.
     *
     * @param   Child $child
     *
     * @return  self
     */
    public function addChild(Child $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove subrecord.
     *
     * @param   Child $child
     *
     * @return  self
     */
    public function removeChild(Child $child)
    {
        $this->children->removeElement($child);

        return $this;
    }

    /**
     * Get list of subrecords.
     *
     * @return  ArrayCollection|Child[]
     */
    public function getChildren()
    {
        return $this->children;
    }
}
