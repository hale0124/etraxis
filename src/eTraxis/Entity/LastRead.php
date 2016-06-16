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
 * @ORM\Table(name="last_reads")
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
     * @ORM\JoinColumn(name="record_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $record;

    /**
     * @var User User.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var int Unix Epoch timestamp when the record has been opened by this user last time.
     *
     * @ORM\Column(name="read_at", type="integer")
     */
    private $readAt;

    /**
     * Constructor.
     *
     * @param   Record $record
     * @param   User   $user
     */
    public function __construct(Record $record, User $user)
    {
        $this->record = $record;
        $this->user   = $user;
        $this->readAt = time();
    }
}
