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
 * Record events (history).
 *
 * @ORM\Table(name="tbl_events",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_events", columns={"record_id", "originator_id", "event_type", "event_time", "event_param"})
 *            },
 *            indexes={
 *                @ORM\Index(name="ix_record", columns={"record_id"}),
 *                @ORM\Index(name="ix_evts_comb", columns={"event_id", "record_id"})
 *            })
 * @ORM\Entity(repositoryClass="eTraxis\Repository\EventsRepository")
 */
class Event
{
    // Event types.
    const RECORD_CREATED       = 1;
    const RECORD_ASSIGNED      = 2;
    const RECORD_MODIFIED      = 3;
    const STATE_CHANGED        = 4;
    const RECORD_POSTPONED     = 5;
    const RECORD_RESUMED       = 6;
    const COMMENT_ADDED        = 7;
    const FILE_ADDED           = 8;
    const FILE_REMOVED         = 9;
    const RECORD_CLONED        = 10;
    const SUBRECORD_ATTACHED   = 11;
    const SUBRECORD_DETACHED   = 12;
    const CONFIDENTIAL_COMMENT = 13;
    const RECORD_REOPENED      = 14;

    /**
     * @var int Unique ID.
     *
     * @ORM\Column(name="event_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int Record ID.
     *
     * @ORM\Column(name="record_id", type="integer")
     */
    private $recordId;

    /**
     * @var int User ID who raised the event.
     *
     * @ORM\Column(name="originator_id", type="integer")
     */
    private $userId;

    /**
     * @var int Type of the event.
     *
     * @ORM\Column(name="event_type", type="integer")
     */
    private $type;

    /**
     * @var int Unix Epoch timestamp when the event has been registered.
     *
     * @ORM\Column(name="event_time", type="integer")
     */
    private $createdAt;

    /**
     * @var int Parameter of the event. Depends on event type as following:
     *
     *          "created"        - ID of first (initial) state of the created record
     *          "modified"       - NULL (not used)
     *          "assigned"       - ID of the user, the record has been assigned to
     *          "postponed"      - Unix Epoch timestamp, when the record should be automatically resumed
     *          "resumed"        - NULL (not used)
     *          "reopened"       - ID of new state of the reopened record
     *          "cloned"         - ID of the original record
     *          "state-changed"  - ID of the state, the record has been changed to
     *          "commented"      - NULL (not used)
     *          "confidential"   - NULL (not used)
     *          "file-added"     - NULL (not used)
     *          "file-removed"   - NULL (not used)
     *          "child-attached" - ID of the attached record
     *          "child-detached" - ID of the detached record
     *
     * @ORM\Column(name="event_param", type="integer", nullable=true)
     */
    private $parameter;

    /**
     * @var Record Record.
     *
     * @ORM\ManyToOne(targetEntity="Record", inversedBy="history")
     * @ORM\JoinColumn(name="record_id", referencedColumnName="record_id")
     */
    private $record;

    /**
     * @var User User who raised the event.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="originator_id", referencedColumnName="account_id")
     */
    private $user;

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
     * @param   int $recordId
     *
     * @return  self
     */
    public function setRecordId($recordId)
    {
        $this->recordId = $recordId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getRecordId()
    {
        return $this->recordId;
    }

    /**
     * Standard setter.
     *
     * @param   int $userId
     *
     * @return  self
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getUserId()
    {
        return $this->userId;
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
     * @param   int $parameter
     *
     * @return  self
     */
    public function setParameter($parameter)
    {
        $this->parameter = $parameter;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * Standard setter.
     *
     * @param   Record $record
     *
     * @return  self
     */
    public function setRecord(Record $record)
    {
        $this->record = $record;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  Record
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * Standard setter.
     *
     * @param   User $user
     *
     * @return  self
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  User
     */
    public function getUser()
    {
        return $this->user;
    }
}
