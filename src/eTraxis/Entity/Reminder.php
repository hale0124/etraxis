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
 * Reminder.
 *
 * @ORM\Table(name="tbl_reminders",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_reminders", columns={"account_id", "reminder_name"})
 *            })
 * @ORM\Entity
 */
class Reminder
{
    /**
     * @var int Unique ID.
     *
     * @ORM\Column(name="reminder_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int Owner of the reminder.
     *
     * @ORM\Column(name="account_id", type="integer")
     */
    private $userId;

    /**
     * @var string Name of the reminder.
     *
     * @ORM\Column(name="reminder_name", type="string", length=25)
     */
    private $name;

    /**
     * @var string Reminder's email subject.
     *
     * @ORM\Column(name="subject_text", type="string", length=100, nullable=true)
     */
    private $subject;

    /**
     * @var int State of issues to be reminded about.
     *
     * @ORM\Column(name="state_id", type="integer")
     */
    private $stateId;

    /**
     * @var int Group, which should be reminded (ignored if "role" is specified).
     *
     * @ORM\Column(name="group_id", type="integer", nullable=true)
     */
    private $groupId;

    /**
     * @var int System role, which should be reminded. Zero to remind a particular group (specified in "group_id").
     *
     * @ORM\Column(name="group_flag", type="integer")
     */
    private $role;

    /**
     * @var User Owner of the reminder.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="account_id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var State State of issues to be reminded about.
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="state_id", onDelete="CASCADE")
     */
    private $state;

    /**
     * @var Group Group, which should be reminded (ignored if "role" is specified).
     *
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="group_id", onDelete="CASCADE")
     */
    private $group;

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
     * @param   int $groupId
     *
     * @return  self
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Standard setter.
     *
     * @param   int $role
     *
     * @return  self
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getRole()
    {
        return $this->role;
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
     * @param   Group $group
     *
     * @return  self
     */
    public function setGroup(Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  Group
     */
    public function getGroup()
    {
        return $this->group;
    }
}
