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
    // Constraints.
    const MAX_NAME        = 25;
    const MAX_CARBON_COPY = 50;

    // Notification types.
    const TYPE_ALL      = 1;
    const TYPE_PROJECT  = 2;
    const TYPE_TEMPLATE = 3;

    // Notification events.
    const NOTIFY_RECORD_CREATED     = 0x0001;
    const NOTIFY_RECORD_ASSIGNED    = 0x0002;
    const NOTIFY_RECORD_MODIFIED    = 0x0004;
    const NOTIFY_STATE_CHANGED      = 0x0008;
    const NOTIFY_RECORD_POSTPONED   = 0x0010;
    const NOTIFY_RECORD_RESUMED     = 0x0020;
    const NOTIFY_COMMENT_ADDED      = 0x0040;
    const NOTIFY_FILE_ADDED         = 0x0080;
    const NOTIFY_FILE_REMOVED       = 0x0100;
    const NOTIFY_RECORD_CLONED      = 0x0200;
    const NOTIFY_SUBRECORD_ATTACHED = 0x0400;
    const NOTIFY_SUBRECORD_DETACHED = 0x0800;
    const NOTIFY_RECORD_REOPENED    = 0x1000;

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="subscribe_id", type="integer")
     */
    private $id;

    /**
     * @var User Owner of the notification.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="account_id", nullable=false, referencedColumnName="account_id", onDelete="CASCADE")
     */
    private $user;

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
     * @var int Whether the notification is enabled or disabled.
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
}
