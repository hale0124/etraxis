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

use Doctrine\ORM\Mapping as ORM;

/**
 * Group.
 *
 * @ORM\Table(name="tbl_groups",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_groups", columns={"project_id", "group_name"})
 *            })
 * @ORM\Entity
 */
class Group
{
    /**
     * @var int Unique ID.
     *
     * @ORM\Column(name="group_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int|NULL Project ID of the group.
     *
     * @ORM\Column(name="project_id", type="integer", nullable=true)
     */
    private $projectId;

    /**
     * @var string Name of the group.
     *
     * @ORM\Column(name="group_name", type="string", length=25)
     */
    private $name;

    /**
     * @var string Optional description of the group.
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     */
    private $description;

    /**
     * @var Project|NULL Project of the group (NULL if the group is global).
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="groups")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="project_id")
     */
    private $project;

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
     * @param   int $projectId
     *
     * @return  self
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getProjectId()
    {
        return $this->projectId;
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
     * @param   Project|NULL $project
     *
     * @return  self
     */
    public function setProject(Project $project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  Project
     */
    public function getProject()
    {
        return $this->project;
    }
}
