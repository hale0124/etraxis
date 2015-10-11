<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
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
 * Group.
 *
 * @ORM\Table(name="tbl_groups",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_groups", columns={"project_id", "group_name"})
 *            })
 * @ORM\Entity
 * @Assert\UniqueEntity(fields={"project", "name"}, message="group.conflict.name", ignoreNull=false)
 */
class Group
{
    const MAX_NAME        = 25;
    const MAX_DESCRIPTION = 100;

    /**
     * @var int Unique ID.
     *
     * @ORM\Column(name="group_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int Project ID of the group.
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
     * @var Project Project of the group (NULL if the group is global).
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="groups")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="project_id", onDelete="CASCADE")
     */
    private $project;

    /**
     * @var ArrayCollection List of members.
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="groups")
     * @ORM\JoinTable(name="tbl_membership",
     *                joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="group_id")},
     *                inverseJoinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="account_id")})
     * @ORM\OrderBy({"fullname" = "ASC"})
     */
    private $users;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * @param   Project $project
     *
     * @return  self
     */
    public function setProject(Project $project = null)
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

    /**
     * Add user to the group.
     *
     * @param   User $user
     *
     * @return  self
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user from the group.
     *
     * @param   User $user
     *
     * @return  self
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * Get list of group members.
     *
     * @return  ArrayCollection|User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Returns whether group is global.
     *
     * @return  bool
     */
    public function isGlobal()
    {
        return !$this->projectId;
    }
}
