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
 * Issue watcher.
 *
 * @ORM\Table(name="tbl_record_subscribes")
 * @ORM\Entity
 */
class Watcher
{
    /**
     * @var int Watched issue ID.
     *
     * @ORM\Column(name="record_id", type="integer")
     * @ORM\Id
     */
    private $issueId;

    /**
     * @var int Watcher ID.
     *
     * @ORM\Column(name="account_id", type="integer")
     * @ORM\Id
     */
    private $watcherId;

    /**
     * @var int Initiator ID who set this user watch the issue.
     *
     * @ORM\Column(name="subscribed_by", type="integer")
     * @ORM\Id
     */
    private $initiatorId;

    /**
     * @var Issue Watched issue.
     *
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="watchers")
     * @ORM\JoinColumn(name="record_id", referencedColumnName="record_id")
     */
    private $issue;

    /**
     * @var User Watcher.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="account_id")
     */
    private $watcher;

    /**
     * @var User Initiator who set this user watch the issue.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="subscribed_by", referencedColumnName="account_id")
     */
    private $initiator;

    /**
     * Standard setter.
     *
     * @param   int $issueId
     *
     * @return  self
     */
    public function setIssueId($issueId)
    {
        $this->issueId = $issueId;

        return $this;
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
     * Standard setter.
     *
     * @param   int $watcherId
     *
     * @return  self
     */
    public function setWatcherId($watcherId)
    {
        $this->watcherId = $watcherId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getWatcherId()
    {
        return $this->watcherId;
    }

    /**
     * Standard setter.
     *
     * @param   int $initiatorId
     *
     * @return  self
     */
    public function setInitiatorId($initiatorId)
    {
        $this->initiatorId = $initiatorId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getInitiatorId()
    {
        return $this->initiatorId;
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
     * @param   User $watcher
     *
     * @return  self
     */
    public function setWatcher(User $watcher)
    {
        $this->watcher = $watcher;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  User
     */
    public function getWatcher()
    {
        return $this->watcher;
    }

    /**
     * Standard setter.
     *
     * @param   User $initiator
     *
     * @return  self
     */
    public function setInitiator(User $initiator)
    {
        $this->initiator = $initiator;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  User
     */
    public function getInitiator()
    {
        return $this->initiator;
    }
}
