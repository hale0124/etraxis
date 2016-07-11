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
 * @ORM\Table(name="attachments",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(columns={"event_id"})
 *            })
 * @ORM\Entity
 */
class Attachment
{
    // Constraints.
    const MAX_NAME = 100;
    const MAX_TYPE = 255;

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var Event Event.
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(name="event_id", nullable=false, referencedColumnName="id", onDelete="CASCADE")
     */
    private $event;

    /**
     * @var string Attachment name.
     *
     * @ORM\Column(name="file_name", type="string", length=100)
     */
    private $name;

    /**
     * @var int Attachment size.
     *
     * @ORM\Column(name="file_size", type="integer")
     */
    private $size;

    /**
     * @var string Attachment MIME type.
     *
     * @ORM\Column(name="mime_type", type="string", length=255)
     */
    private $type;

    /**
     * @var bool Whether attachment was deleted.
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    private $isDeleted;
}
