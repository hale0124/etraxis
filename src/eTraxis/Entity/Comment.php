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
 * @ORM\Table(name="comments",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(columns={"event_id"})
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
     * @var string Text of the comment.
     *
     * @ORM\Column(name="comment_text", type="text")
     */
    private $text;

    /**
     * @var bool Whether comment is private.
     *
     * @ORM\Column(name="is_private", type="boolean")
     */
    private $isPrivate;
}
