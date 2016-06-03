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
 * @ORM\Table(name="watchers")
 * @ORM\Entity
 */
class Watcher
{
    /**
     * @var Record Watched record.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Record", inversedBy="watchers")
     * @ORM\JoinColumn(name="record_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $record;

    /**
     * @var User Watcher.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="watcher_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $watcher;

    /**
     * @var User Initiator who set this user watch the record.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="initiator_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $initiator;
}
