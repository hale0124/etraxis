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
 * Last reading time of record.
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
     * @var Record Record.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Record")
     * @ORM\JoinColumn(name="record_id", referencedColumnName="record_id", onDelete="CASCADE")
     */
    private $record;

    /**
     * @var User User.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="account_id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var int Unix Epoch timestamp when the record has been read by this user last time.
     *
     * @ORM\Column(name="read_time", type="integer")
     */
    private $readAt;

    /**
     * Property setter.
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
     * Property getter.
     *
     * @return  Record
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * Property setter.
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
     * Property getter.
     *
     * @return  User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Property setter.
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
     * Property getter.
     *
     * @return  int
     */
    public function getReadAt()
    {
        return $this->readAt;
    }
}
