<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification.
 *
 * @ORM\Table(name="tbl_subscribes",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_subscribes", columns={"account_id", "subscribe_name"})
 *            })
 * @ORM\Entity
 */
class Notification
{
    // Notification types.
    const TYPE_ALL      = 1;
    const TYPE_PROJECT  = 2;
    const TYPE_TEMPLATE = 3;

    // Notification events.
    const NOTIFY_ISSUE_CREATED    = 0x0001;
    const NOTIFY_ISSUE_ASSIGNED   = 0x0002;
    const NOTIFY_ISSUE_MODIFIED   = 0x0004;
    const NOTIFY_STATE_CHANGED    = 0x0008;
    const NOTIFY_ISSUE_POSTPONED  = 0x0010;
    const NOTIFY_ISSUE_RESUMED    = 0x0020;
    const NOTIFY_COMMENT_ADDED    = 0x0040;
    const NOTIFY_FILE_ADDED       = 0x0080;
    const NOTIFY_FILE_REMOVED     = 0x0100;
    const NOTIFY_ISSUE_CLONED     = 0x0200;
    const NOTIFY_SUBISSUE_ADDED   = 0x0400;
    const NOTIFY_SUBISSUE_REMOVED = 0x0800;
    const NOTIFY_ISSUE_REOPENED   = 0x1000;

    /**
     * @var int Unique ID.
     *
     * @ORM\Column(name="subscribe_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int User ID.
     *
     * @ORM\Column(name="account_id", type="integer")
     */
    private $userId;

    /**
     * @var string Name of the notification.
     *
     * @ORM\Column(name="subscribe_name", type="string", length=25)
     */
    private $name;

    /**
     * @var string Emails carbon copy.
     *
     * @ORM\Column(name="carbon_copy", type="string", length=50, nullable=true)
     */
    private $carbonCopy;

    /**
     * @var bool Whether the notification is enabled or disabled.
     *
     * @ORM\Column(name="is_activated", type="integer")
     */
    private $isActivated;

    /**
     * @var int Type of the notification (events scope).
     *
     * @ORM\Column(name="subscribe_type", type="integer")
     */
    private $type;

    /**
     * @var int Events to notify about.
     *
     * @ORM\Column(name="subscribe_flags", type="integer")
     */
    private $events;

    /**
     * @var int Parameter of the notification. Depends on the notification type as following:
     *
     *          "all"      - NULL (not used)
     *          "project"  - ID of the project
     *          "template" - ID of the template
     *
     * @ORM\Column(name="subscribe_param", type="integer", nullable=true)
     */
    private $parameter;

    /**
     * @var User Owner of the notification.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="account_id", onDelete="CASCADE")
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
     * @param   string $carbonCopy
     *
     * @return  self
     */
    public function setCarbonCopy($carbonCopy)
    {
        $this->carbonCopy = $carbonCopy;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getCarbonCopy()
    {
        return $this->carbonCopy;
    }

    /**
     * Standard setter.
     *
     * @param   bool $isActivated
     *
     * @return  self
     */
    public function setActivated($isActivated)
    {
        $this->isActivated = $isActivated ? 1 : 0;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  bool
     */
    public function isActivated()
    {
        return (bool) $this->isActivated;
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
     * @param   int $events
     *
     * @return  self
     */
    public function setEvents($events)
    {
        $this->events = $events;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getEvents()
    {
        return $this->events;
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
