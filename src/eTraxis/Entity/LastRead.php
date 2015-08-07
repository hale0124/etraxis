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
 * Last reading time of issue.
 *
 * @ORM\Table(name="tbl_reads",
 *            indexes={
 *                @ORM\Index(name="ix_rds_comb", columns={"record_id", "account_id", "read_time"})
 *            })
 * @ORM\Entity
 */
class LastRead
{
    /**
     * @var int Issue ID.
     *
     * @ORM\Column(name="record_id", type="integer")
     * @ORM\Id
     */
    private $issueId;

    /**
     * @var int User ID.
     *
     * @ORM\Column(name="account_id", type="integer")
     * @ORM\Id
     */
    private $userId;

    /**
     * @var int Unix Epoch timestamp when the issue has been read by this user last time.
     *
     * @ORM\Column(name="read_time", type="integer")
     */
    private $readAt;

    /**
     * @var Issue Issue.
     *
     * @ORM\ManyToOne(targetEntity="Issue")
     * @ORM\JoinColumn(name="record_id", referencedColumnName="record_id", onDelete="CASCADE")
     */
    private $issue;

    /**
     * @var User User.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="account_id", onDelete="CASCADE")
     */
    private $user;

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
     * @param   int $readAt
     *
     * @return  self
     */
    public function setReadAt($readAt)
    {
        $this->readAt = $readAt;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getReadAt()
    {
        return $this->readAt;
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
