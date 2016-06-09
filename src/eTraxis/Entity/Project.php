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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

/**
 * Project.
 *
 * @ORM\Table(name="projects",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_projects_name", columns={"name"})
 *            })
 * @ORM\Entity(repositoryClass="eTraxis\Repository\ProjectsRepository")
 * @Assert\UniqueEntity(fields={"name"}, message="project.conflict.name")
 */
class Project implements \JsonSerializable
{
    // Constraints.
    const MAX_NAME        = 25;
    const MAX_DESCRIPTION = 100;

    // Actions.
    const DELETE = 'project.delete';

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string Name of the project.
     *
     * @ORM\Column(name="name", type="string", length=25)
     */
    private $name;

    /**
     * @var string Optional description of the project.
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     */
    private $description;

    /**
     * @var int Unix Epoch timestamp when the project has been registered.
     *
     * @ORM\Column(name="created_at", type="integer")
     */
    private $createdAt;

    /**
     * @var bool Whether the project is suspended.
     *           When project is suspended, its records are read-only, and new records cannot be created.
     *
     * @ORM\Column(name="is_suspended", type="boolean")
     */
    private $isSuspended;

    /**
     * @var ArrayCollection List of project groups.
     *
     * @ORM\OneToMany(targetEntity="Group", mappedBy="project", cascade={"remove"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $groups;

    /**
     * @var ArrayCollection List of project templates.
     *
     * @ORM\OneToMany(targetEntity="Template", mappedBy="project")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $templates;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->createdAt = time();

        $this->groups    = new ArrayCollection();
        $this->templates = new ArrayCollection();
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
     * Property setter.
     *
     * @param   string $name
     *
     * @return  self
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
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
     * Property setter.
     *
     * @param   string|null $description
     *
     * @return  self
     */
    public function setDescription(string $description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string|null
     */
    public function getDescription()
    {
        return $this->description;
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
     * Property setter.
     *
     * @param   bool $isSuspended
     *
     * @return  self
     */
    public function setSuspended(bool $isSuspended)
    {
        $this->isSuspended = $isSuspended;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  bool
     */
    public function isSuspended()
    {
        return $this->isSuspended;
    }

    /**
     * Get list of project groups.
     *
     * @return  Group[]
     */
    public function getGroups()
    {
        return $this->groups->toArray();
    }

    /**
     * Get list of project templates.
     *
     * @return  Template[]
     */
    public function getTemplates()
    {
        return $this->templates->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return 'project#' . $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id'          => $this->getId(),
            'name'        => $this->getName(),
            'description' => $this->getDescription(),
            'createdAt'   => date('Y-m-d', $this->getCreatedAt()),
            'isSuspended' => $this->isSuspended(),
        ];
    }
}
