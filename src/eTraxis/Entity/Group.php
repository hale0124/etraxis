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
 * Group.
 *
 * @ORM\Table(name="tbl_groups",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_groups", columns={"project_id", "group_name"})
 *            })
 * @ORM\Entity(repositoryClass="eTraxis\Repository\GroupsRepository")
 * @ORM\EntityListeners({"eTraxis\Entity\EntityListener"})
 * @Assert\UniqueEntity(fields={"project", "name"}, message="group.conflict.name", ignoreNull=false)
 */
class Group extends Entity implements \JsonSerializable
{
    // Constraints.
    const MAX_NAME        = 25;
    const MAX_DESCRIPTION = 100;

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="group_id", type="integer")
     */
    private $id;

    /**
     * @var Project Project of the group (NULL if the group is global).
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="groups")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="project_id", onDelete="CASCADE")
     */
    private $project;

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
     * @var ArrayCollection List of members.
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="groups")
     * @ORM\JoinTable(name="tbl_membership",
     *                joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="group_id")},
     *                inverseJoinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="account_id")})
     * @ORM\OrderBy({"fullname" = "ASC", "username" = "ASC"})
     */
    private $members;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->members = new ArrayCollection();
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
     * Property getter.
     *
     * @return  Project
     */
    public function getProject()
    {
        return $this->project;
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
     * Returns whether group is global.
     *
     * @return  bool
     */
    public function isGlobal()
    {
        return $this->project === null;
    }

    /**
     * Adds user to the group.
     *
     * @param   User $user
     *
     * @return  self
     */
    public function addMember(User $user)
    {
        $this->members[] = $user;

        return $this;
    }

    /**
     * Removes user from the group.
     *
     * @param   User $user
     *
     * @return  self
     */
    public function removeMember(User $user)
    {
        $this->members->removeElement($user);

        return $this;
    }

    /**
     * Gets list of group members.
     *
     * @return  User[]
     */
    public function getMembers()
    {
        return $this->members->toArray();
    }

    /**
     * Gets list of all users who are not members of the group.
     *
     * @return  User[]
     */
    public function getNonMembers()
    {
        $query = $this->manager->createQueryBuilder();

        $query
            ->select('u')
            ->from(User::class, 'u')
            ->orderBy('u.fullname')
            ->addOrderBy('u.username')
        ;

        if (count($this->members) > 0) {
            $query
                ->where($query->expr()->notIn('u', ':members'))
                ->setParameter('members', $this->members)
            ;
        }

        return $query->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id'          => $this->getId(),
            'project'     => $this->project ? $this->project->getId() : null,
            'name'        => $this->getName(),
            'description' => $this->getDescription(),
        ];
    }
}
