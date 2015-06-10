<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------


namespace eTraxis\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Project.
 *
 * @ORM\Table(name="tbl_projects",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_projects", columns={"project_name"})
 *            })
 * @ORM\Entity
 */
class Project
{
    /**
     * @var int Unique ID.
     *
     * @ORM\Column(name="project_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string Name of the project.
     *
     * @ORM\Column(name="project_name", type="string", length=25)
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
     * @ORM\Column(name="start_time", type="integer")
     */
    private $createdAt;

    /**
     * @var bool Whether the project is suspended.
     *           When project is suspended, its issues are read-only, and new issues cannot be created.
     *
     * @ORM\Column(name="is_suspended", type="integer")
     */
    private $isSuspended;

    /**
     * @var ArrayCollection List of project groups.
     *
     * @ORM\OneToMany(targetEntity="Group", mappedBy="project")
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
        $this->groups    = new ArrayCollection();
        $this->templates = new ArrayCollection();
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Standard setter.
     *
     * @param   string $name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Standard setter.
     *
     * @param   string $description
     *
     * @return  self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Standard setter.
     *
     * @param   int $createdAt
     *
     * @return  self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Standard setter.
     *
     * @param   bool $isSuspended
     *
     * @return  self
     */
    public function setSuspended($isSuspended)
    {
        $this->isSuspended = $isSuspended;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  bool
     */
    public function isSuspended()
    {
        return (boolean) $this->isSuspended;
    }

    /**
     * Add group to the project.
     *
     * @param   Group $group
     *
     * @return  self
     */
    public function addGroup(Group $group)
    {
        $this->groups[] = $group;

        return $this;
    }

    /**
     * Remove group from the project.
     *
     * @param   Group $group
     *
     * @return  self
     */
    public function removeGroup(Group $group)
    {
        $this->groups->removeElement($group);

        return $this;
    }

    /**
     * Get list of project groups.
     *
     * @return  ArrayCollection|Group[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add template to the project.
     *
     * @param   Template $template
     *
     * @return  self
     */
    public function addTemplate(Template $template)
    {
        $this->templates[] = $template;

        return $this;
    }

    /**
     * Remove template from the project.
     *
     * @param   Template $template
     *
     * @return  self
     */
    public function removeTemplate(Template $template)
    {
        $this->templates->removeElement($template);

        return $this;
    }

    /**
     * Get list of project templates.
     *
     * @return  ArrayCollection|Template[]
     */
    public function getTemplates()
    {
        return $this->templates;
    }
}
