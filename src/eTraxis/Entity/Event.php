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
use eTraxis\Dictionary\EventType;

/**
 * Record events (history).
 *
 * @ORM\Table(name="events",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(columns={"record_id", "user_id", "type", "created_at", "parameter"})
 *            },
 *            indexes={
 *                @ORM\Index(columns={"record_id"})
 *            })
 * @ORM\Entity
 */
class Event
{
    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var Record Record.
     *
     * @ORM\ManyToOne(targetEntity="Record", inversedBy="events")
     * @ORM\JoinColumn(name="record_id", nullable=false, referencedColumnName="id", onDelete="CASCADE")
     */
    private $record;

    /**
     * @var User User who raised the event.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", nullable=false, referencedColumnName="id")
     */
    private $user;

    /**
     * @var string Type of the event.
     *
     * @ORM\Column(name="type", type="string", length=20)
     */
    private $type;

    /**
     * @var int Unix Epoch timestamp when the event has been registered.
     *
     * @ORM\Column(name="created_at", type="integer")
     */
    private $createdAt;

    /**
     * @var int Parameter of the event. Depends on event type as following:
     *
     *      RECORD_CREATED     - ID of first (initial) state of the created record
     *      RECORD_EDITED      - NULL (not used)
     *      RECORD_ASSIGNED    - ID of the user, the record has been assigned to
     *      STATE_CHANGED      - ID of the state, the record has been changed to
     *      RECORD_POSTPONED   - Unix Epoch timestamp, when the record should be automatically resumed
     *      RECORD_RESUMED     - NULL (not used)
     *      RECORD_CLONED      - ID of the original record
     *      RECORD_REOPENED    - ID of new state of the reopened record
     *      PUBLIC_COMMENT     - NULL (not used)
     *      PRIVATE_COMMENT    - NULL (not used)
     *      FILE_ATTACHED      - NULL (not used)
     *      FILE_DELETED       - ID of the deleted attachment
     *      SUBRECORD_ATTACHED - ID of the attached record
     *      SUBRECORD_DETACHED - ID of the detached record
     *
     * @ORM\Column(name="parameter", type="integer", nullable=true)
     */
    private $parameter;

    /**
     * Constructor.
     *
     * @param   Record $record
     * @param   User   $user
     * @param   string $type
     * @param   int    $parameter
     */
    public function __construct(Record $record, User $user, string $type, int $parameter = null)
    {
        $this->record    = $record;
        $this->user      = $user;
        $this->parameter = $parameter;

        if (EventType::has($type)) {
            $this->type = $type;
        }

        $this->createdAt = time();
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
     * Property getter.
     *
     * @return  Record
     */
    public function getRecord()
    {
        return $this->record;
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
     * Property getter.
     *
     * @return  string
     */
    public function getType()
    {
        return $this->type;
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
    public function getParameter()
    {
        return $this->parameter;
    }
}
