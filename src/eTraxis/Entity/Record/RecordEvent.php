<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Record;

use eTraxis\Entity\User;

/**
 * Record's event.
 */
class RecordEvent
{
    /**
     * @var User User who raised the event.
     */
    protected $user;

    /**
     * @var string Type of the event.
     */
    protected $type;

    /**
     * @var int Unix Epoch timestamp when the event has been registered.
     */
    protected $createdAt;

    /**
     * @var string User-friendly parameter of the event. Depends on event type as following:
     *
     *      RECORD_CREATED     - Name of first (initial) state of the created record
     *      RECORD_EDITED      - NULL (not used)
     *      RECORD_ASSIGNED    - Full name of the user, the record has been assigned to
     *      STATE_CHANGED      - Name of the state, the record has been changed to
     *      RECORD_POSTPONED   - Unix Epoch timestamp, when the record should be automatically resumed
     *      RECORD_RESUMED     - NULL (not used)
     *      RECORD_CLONED      - ID of the original record
     *      RECORD_REOPENED    - Name of new state of the reopened record
     *      PUBLIC_COMMENT     - NULL (not used)
     *      PRIVATE_COMMENT    - NULL (not used)
     *      FILE_ATTACHED      - Name of the attached file
     *      FILE_DELETED       - Name of the deleted file
     *      SUBRECORD_ATTACHED - ID of the attached record
     *      SUBRECORD_DETACHED - ID of the detached record
     */
    protected $parameter;

    /**
     * Constructor.
     *
     * @param   User   $user
     * @param   string $type
     * @param   int    $createdAt
     * @param   string $parameter
     */
    public function __construct(User $user, string $type, int $createdAt, string $parameter = null)
    {
        $this->user      = $user;
        $this->type      = $type;
        $this->createdAt = $createdAt;
        $this->parameter = $parameter;
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
