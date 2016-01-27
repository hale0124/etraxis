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
 * Record watcher.
 *
 * @ORM\Table(name="tbl_record_subscribes")
 * @ORM\Entity
 */
class Watcher
{
    /**
     * @var int Watched record ID.
     *
     * @ORM\Column(name="record_id", type="integer")
     * @ORM\Id
     */
    private $recordId;

    /**
     * @var int Watcher ID.
     *
     * @ORM\Column(name="account_id", type="integer")
     * @ORM\Id
     */
    private $watcherId;

    /**
     * @var int Initiator ID who set this user watch the record.
     *
     * @ORM\Column(name="subscribed_by", type="integer")
     * @ORM\Id
     */
    private $initiatorId;

    /**
     * @var Record Watched record.
     *
     * @ORM\ManyToOne(targetEntity="Record", inversedBy="watchers")
     * @ORM\JoinColumn(name="record_id", referencedColumnName="record_id", onDelete="CASCADE")
     */
    private $record;

    /**
     * @var User Watcher.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="account_id", onDelete="CASCADE")
     */
    private $watcher;

    /**
     * @var User Initiator who set this user watch the record.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="subscribed_by", referencedColumnName="account_id", onDelete="CASCADE")
     */
    private $initiator;

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
