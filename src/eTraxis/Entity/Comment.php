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

    /**
     * Creates new comment in specified record.
     *
     * @param   Record $record    Target record.
     * @param   User   $user      Author of the comment.
     * @param   string $text      Text of the comment.
     * @param   bool   $isPrivate Whether comment is private.
     */
    public function __construct(Record $record, User $user, string $text, bool $isPrivate = false)
    {
        $this->event = new Event($record, $user, $isPrivate ? EventType::PRIVATE_COMMENT : EventType::PUBLIC_COMMENT);

        $this->text      = $text;
        $this->isPrivate = $isPrivate;
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
     * @return  Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Property getter.
     *
     * @return  bool
     */
    public function isPrivate()
    {
        return $this->isPrivate;
    }
}
