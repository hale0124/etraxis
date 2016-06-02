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
 * @ORM\Entity
 */
class Record
{
    // Constraints.
    const MAX_SUBJECT = 250;

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="record_id", type="integer")
     */
    private $id;

    /**
     * @var string Subject of the record.
     *
     * @ORM\Column(name="subject", type="string", length=250)
     */
    private $subject;

    /**
     * @var State Current state of the record.
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state_id", nullable=false, referencedColumnName="state_id")
     */
    private $state;

    /**
     * @var User Author of the the record.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="creator_id", nullable=false, referencedColumnName="account_id")
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
     * Constructor.
     */
    public function __construct()
    {
        $this->responsible = null;

        $this->createdAt = time();
        $this->changedAt = time();
        $this->closedAt  = null;
        $this->resumedAt = 0;

        $this->history  = new ArrayCollection();
        $this->watchers = new ArrayCollection();
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Property setter.
     *
     * @param   string $subject
     *
     * @return  self
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Property getter.
     *
     * @return  State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Property getter.
     *
     * @return  User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Property getter.
     *
     * @return  User
     */
    public function getResponsible()
    {
        return $this->responsible;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getChangedAt()
    {
        return $this->changedAt;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getClosedAt()
    {
        return $this->closedAt;
    }

    /**
     * Get record history.
     *
     * @return  Event[]
     */
    public function getHistory()
    {
        return $this->history->toArray();
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
     * @return  Watcher[]
     */
    public function getWatchers()
    {
        return $this->watchers->toArray();
    }
}
