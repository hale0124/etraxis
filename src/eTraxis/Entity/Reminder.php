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
    // Constraints.
    const MAX_NAME    = 25;
    const MAX_SUBJECT = 100;

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="reminder_id", type="integer")
     */
    private $id;

    /**
     * @var User Owner of the reminder.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="account_id", nullable=false, referencedColumnName="account_id", onDelete="CASCADE")
     */
    private $user;

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
     * @var State State of records to be reminded about.
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state_id", nullable=false, referencedColumnName="state_id", onDelete="CASCADE")
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
     * @var int System role, which should be reminded. Zero to remind a particular group (specified in "group_id").
     *
     * @ORM\Column(name="group_flag", type="integer")
     */
    private $role;
}
