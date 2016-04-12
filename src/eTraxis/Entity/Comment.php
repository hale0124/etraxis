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
 * Comment.
 *
 * @ORM\Table(name="tbl_comments",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_comments", columns={"event_id"})
 *            })
 * @ORM\Entity
 */
class Comment
{
    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="comment_id", type="integer")
     */
    private $id;

    /**
     * @var Event Event.
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(name="event_id", nullable=false, referencedColumnName="event_id", onDelete="CASCADE")
     */
    private $event;

    /**
     * @var int Whether comment is private.
     *
     * @ORM\Column(name="is_confidential", type="integer")
     */
    private $isPrivate;

    /**
     * @var string Comment body.
     *
     * @ORM\Column(name="comment_body", type="text")
     */
    private $comment;
}
