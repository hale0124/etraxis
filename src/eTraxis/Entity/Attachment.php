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
 * Attachment.
 *
 * @ORM\Table(name="tbl_attachments",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_attachments", columns={"event_id"})
 *            })
 * @ORM\Entity
 */
class Attachment
{
    // Constraints.
    const MAX_NAME = 100;
    const MAX_TYPE = 100;

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="attachment_id", type="integer")
     */
    private $id;

    /**
     * @var Event Event.
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(name="event_id", nullable=false, referencedColumnName="event_id")
     */
    private $event;

    /**
     * @var string Attachment name.
     *
     * @ORM\Column(name="attachment_name", type="string", length=100)
     */
    private $name;

    /**
     * @var string Attachment type.
     *
     * @ORM\Column(name="attachment_type", type="string", length=100)
     */
    private $type;

    /**
     * @var int Attachment size.
     *
     * @ORM\Column(name="attachment_size", type="integer")
     */
    private $size;

    /**
     * @var int Whether attachment is removed.
     *
     * @ORM\Column(name="is_removed", type="integer")
     */
    private $isRemoved;
}
