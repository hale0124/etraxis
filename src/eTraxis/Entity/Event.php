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
 * Issue events (history).
 *
 * @ORM\Table(name="tbl_events",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_events", columns={"record_id", "originator_id", "event_type", "event_time", "event_param"})
 *            },
 *            indexes={
 *                @ORM\Index(name="ix_record", columns={"record_id"}),
 *                @ORM\Index(name="ix_evts_comb", columns={"event_id", "record_id"})
 *            })
 * @ORM\Entity
 */
class Event
{
    // Event types.
    const ISSUE_CREATED        = 1;
    const ISSUE_ASSIGNED       = 2;
    const ISSUE_MODIFIED       = 3;
    const STATE_CHANGED        = 4;
    const ISSUE_POSTPONED      = 5;
    const ISSUE_RESUMED        = 6;
    const COMMENT_ADDED        = 7;
    const FILE_ADDED           = 8;
    const FILE_REMOVED         = 9;
    const ISSUE_CLONED         = 10;
    const SUBISSUE_ADDED       = 11;
    const SUBISSUE_REMOVED     = 12;
    const CONFIDENTIAL_COMMENT = 13;
    const ISSUE_REOPENED       = 14;

    /**
     * @var int Unique ID.
     *
     * @ORM\Column(name="event_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int Issue ID.
     *
     * @ORM\Column(name="record_id", type="integer")
     */
    private $issueId;

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
     *          "created"       - ID of first (initial) state of the created issue
     *          "modified"      - NULL (not used)
     *          "assigned"      - ID of the user, the issue has been assigned to
     *          "postponed"     - Unix Epoch timestamp, when the issue should be automatically resumed
     *          "resumed"       - NULL (not used)
     *          "reopened"      - ID of new state of the reopened issue
     *          "cloned"        - ID of the original issue
     *          "state-changed" - ID of the state, the issue has been changed to
     *          "commented"     - NULL (not used)
     *          "confidential"  - NULL (not used)
     *          "file-added"    - NULL (not used)
     *          "file-removed"  - NULL (not used)
     *          "child-added"   - ID of the added issue
     *          "child-removed" - ID of the removed issue
     *
     * @ORM\Column(name="event_param", type="integer", nullable=true)
     */
    private $parameter;

    /**
     * @var Issue Issue.
     *
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="history")
     * @ORM\JoinColumn(name="record_id", referencedColumnName="record_id")
     */
    private $issue;

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
     * Standard getter.
     *
     * @return  int
     */
    public function getIssueId()
    {
        return $this->issueId;
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
     * @param   Issue $issue
     *
     * @return  self
     */
    public function setIssue(Issue $issue)
    {
        $this->issue = $issue;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  Issue
     */
    public function getIssue()
    {
        return $this->issue;
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
