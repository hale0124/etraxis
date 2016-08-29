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
use eTraxis\Dictionary\MimeType;
use Ramsey\Uuid\Uuid;

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
     * @ORM\ManyToOne(targetEntity="Event", cascade="persist")
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
     * @var string Attachment UUID.
     *
     * @ORM\Column(name="uuid", type="string", length=32)
     */
    private $uuid;

    /**
     * @var bool Whether attachment was deleted.
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    private $isDeleted;

    /**
     * Attaches new file to specified record.
     *
     * @param   Record $record    Target record.
     * @param   User   $user      User attaching the file.
     * @param   string $name      File name.
     * @param   int    $size      File size.
     * @param   string $type      MIME type.
     */
    public function __construct(Record $record, User $user, string $name, int $size, string $type)
    {
        $this->event = new Event($record, $user, EventType::FILE_ATTACHED);

        $this->name = $name;
        $this->size = $size;
        $this->type = $type;
        $this->uuid = Uuid::uuid4()->getHex();

        $this->isDeleted = false;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getSize()
    {
        return $this->size;
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
     * @return  string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Returns name of image file which corresponds to attachment's MIME type.
     *
     * @return  string
     */
    public function getMimeImage()
    {
        return MimeType::get($this->type);
    }

    /**
     * Property setter.
     *
     * @param   bool $isDeleted
     *
     * @return  self
     */
    public function setDeleted(bool $isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  bool
     */
    public function isDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Returns absolute path to the file.
     *
     * @param   string $directory The containing folder.
     *
     * @return  string
     */
    public function getAbsolutePath(string $directory): string
    {
        return realpath($directory) . DIRECTORY_SEPARATOR . $this->uuid;
    }
}
