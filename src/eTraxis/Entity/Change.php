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
 * Record change.
 *
 * @ORM\Table(name="changes",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_changes", columns={"event_id", "field_id"})
 *            })
 * @ORM\Entity
 */
class Change
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
     * @var Event Changing event.
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(name="event_id", nullable=false, referencedColumnName="id", onDelete="CASCADE")
     */
    private $event;

    /**
     * @var Field Changed field. NULL means record's subject.
     *
     * @ORM\ManyToOne(targetEntity="Field")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id")
     */
    private $field;

    /**
     * @var int Old value of the field. Depends on field type (see "FieldValue::$value" for explanation).
     *
     * @ORM\Column(name="old_value", type="integer", nullable=true)
     */
    private $oldValue;

    /**
     * @var int New value of the field. Depends on field type (see "FieldValue::$value" for explanation).
     *
     * @ORM\Column(name="new_value", type="integer", nullable=true)
     */
    private $newValue;
}
