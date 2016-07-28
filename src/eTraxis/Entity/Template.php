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
use eTraxis\Dictionary;
use eTraxis\Security\CurrentUser;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

/**
 * Template.
 *
 * @ORM\Table(name="templates",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(columns={"project_id", "name"}),
 *                @ORM\UniqueConstraint(columns={"project_id", "prefix"})
 *            })
 * @ORM\Entity(repositoryClass="eTraxis\Repository\TemplatesRepository")
 * @ORM\EntityListeners({"eTraxis\Entity\EntityListener"})
 * @Assert\UniqueEntity(fields={"project", "name"}, message="template.conflict.name")
 * @Assert\UniqueEntity(fields={"project", "prefix"}, message="template.conflict.prefix")
 */
class Template extends Entity implements \JsonSerializable
{
    // Constraints.
    const MAX_NAME        = 50;
    const MAX_PREFIX      = 5;
    const MAX_DESCRIPTION = 100;

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var Project Project of the template.
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="templates")
     * @ORM\JoinColumn(name="project_id", nullable=false, referencedColumnName="id", onDelete="CASCADE")
     */
    private $project;

    /**
     * @var string Name of the template.
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string Prefix of the template (used as a prefix in ID of records, created using this template).
     *
     * @ORM\Column(name="prefix", type="string", length=3)
     */
    private $prefix;

    /**
     * @var int When a record remains opened more than this amount of days it is displayed in red in the list of records.
     *
     * @ORM\Column(name="critical_age", type="integer", nullable=true)
     */
    private $criticalAge;

    /**
     * @var int When a record is closed a user cannot change its state anymore, but one still can modify its fields,
     *          add comments and attach files. If frozen time is specified it will be allowed to modify record this
     *          amount of days after its closure. After that record will become read-only. If this attribute is not
     *          specified, record will never become read-only.
     *
     * @ORM\Column(name="frozen_time", type="integer", nullable=true)
     */
    private $frozenTime;

    /**
     * @var string Optional description of the template.
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     */
    private $description;

    /**
     * @var bool Whether the template is locked for edition.
     *
     * @ORM\Column(name="is_locked", type="boolean")
     */
    private $isLocked;

    /**
     * @var ArrayCollection List of template states.
     *
     * @ORM\OneToMany(targetEntity="State", mappedBy="template")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $states;

    /**
     * @var ArrayCollection List of role permissions.
     *
     * @ORM\OneToMany(targetEntity="TemplateRolePermission", mappedBy="template", cascade={"persist"})
     */
    private $rolePermissions;

    /**
     * @var ArrayCollection List of group permissions.
     *
     * @ORM\OneToMany(targetEntity="TemplateGroupPermission", mappedBy="template", cascade={"persist"})
     */
    private $groupPermissions;

    /**
     * @var string[] List of permissions granted to current user.
     */
    private $userPermissions;

    /**
     * @var int ID of the current user.
     */
    private $currentUser;

    /**
     * Creates new template in the specified project.
     *
     * @param   Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;

        $this->states           = new ArrayCollection();
        $this->rolePermissions  = new ArrayCollection();
        $this->groupPermissions = new ArrayCollection();
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
     * @param   string $prefix
     *
     * @return  self
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Property setter.
     *
     * @param   int|null $criticalAge
     *
     * @return  self
     */
    public function setCriticalAge(int $criticalAge = null)
    {
        $this->criticalAge = $criticalAge;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int|null
     */
    public function getCriticalAge()
    {
        return $this->criticalAge;
    }

    /**
     * Property setter.
     *
     * @param   int|null $frozenTime
     *
     * @return  self
     */
    public function setFrozenTime(int $frozenTime = null)
    {
        $this->frozenTime = $frozenTime;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int|null
     */
    public function getFrozenTime()
    {
        return $this->frozenTime;
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
     * Property setter.
     *
     * @param   bool $isLocked
     *
     * @return  self
     */
    public function setLocked(bool $isLocked)
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  bool
     */
    public function isLocked()
    {
        return $this->isLocked;
    }

    /**
     * Returns list of template states.
     *
     * @return  State[]
     */
    public function getStates()
    {
        return $this->states->toArray();
    }

    /**
     * Returns initial state.
     *
     * @return  State|null
     */
    public function getInitialState()
    {
        $states = $this->states->filter(function (State $state) {
            return $state->getType() === Dictionary\StateType::IS_INITIAL;
        });

        return count($states) === 0 ? null : $states->first();
    }

    /**
     * Sets permissions of specified role.
     *
     * @param   string   $role
     * @param   string[] $permissions
     *
     * @return  self
     */
    public function setRolePermissions(string $role, array $permissions)
    {
        $isSpecialRole = in_array($role, [Dictionary\SystemRole::AUTHOR, Dictionary\SystemRole::RESPONSIBLE]);

        // "Author" and "Responsible" roles are always granted to view their records.
        if ($isSpecialRole && !in_array(Dictionary\TemplatePermission::VIEW_RECORDS, $permissions)) {
            $permissions[] = Dictionary\TemplatePermission::VIEW_RECORDS;
        }

        $toAdd = array_unique(array_diff($permissions, $this->getRolePermissions($role)));

        // Remove extra permissions.
        foreach ($this->rolePermissions as $key => $permission) {
            /** @var TemplateRolePermission $permission */
            if ($permission->getRole() === $role) {
                if (!in_array($permission->getPermission(), $permissions)) {
                    $this->rolePermissions->remove($key);
                    $this->manager->remove($permission);
                }
            }
        }

        // Grant required permissions.
        foreach ($toAdd as $permission) {

            if (!Dictionary\TemplatePermission::has($permission)) {
                continue;
            }

            // "Author" and "Responsible" roles can't create their records (as their records already exist).
            if ($isSpecialRole && $permission === Dictionary\TemplatePermission::CREATE_RECORDS)
            {
                continue;
            }

            $element = new TemplateRolePermission($this, $role, $permission);
            $this->rolePermissions->add($element);
        }

        return $this;
    }

    /**
     * Returns permissions of specified role.
     *
     * @param   string $role
     *
     * @return  string[]
     */
    public function getRolePermissions(string $role)
    {
        // Filter all permissions by the role.
        $permissions = $this->rolePermissions->filter(function (TemplateRolePermission $permission) use ($role) {
            return $permission->getRole() === $role;
        });

        // Retrieve the permission part.
        $filtered = $permissions->map(function (TemplateRolePermission $permission) {
            return $permission->getPermission();
        });

        return array_values($filtered->toArray());
    }

    /**
     * Sets permissions of specified group.
     *
     * @param   Group    $group
     * @param   string[] $permissions
     *
     * @return  self
     */
    public function setGroupPermissions(Group $group, array $permissions)
    {
        $toAdd = array_unique(array_diff($permissions, $this->getGroupPermissions($group)));

        // Remove extra permissions.
        foreach ($this->groupPermissions as $key => $permission) {
            /** @var TemplateGroupPermission $permission */
            if ($permission->getGroup() === $group) {
                if (!in_array($permission->getPermission(), $permissions)) {
                    $this->groupPermissions->remove($key);
                    $this->manager->remove($permission);
                }
            }
        }

        // Grant required permissions.
        foreach ($toAdd as $permission) {

            if (!Dictionary\TemplatePermission::has($permission)) {
                continue;
            }

            $element = new TemplateGroupPermission($this, $group, $permission);
            $this->groupPermissions->add($element);
        }

        return $this;
    }

    /**
     * Returns permissions of specified group.
     *
     * @param   Group $group
     *
     * @return  string[]
     */
    public function getGroupPermissions(Group $group)
    {
        // Filter all permissions by the group.
        $permissions = $this->groupPermissions->filter(function (TemplateGroupPermission $permission) use ($group) {
            return $permission->getGroup() === $group;
        });

        // Retrieve the permission part.
        $filtered = $permissions->map(function (TemplateGroupPermission $permission) {
            return $permission->getPermission();
        });

        return array_values($filtered->toArray());
    }

    /**
     * Checks whether specified role is granted for specified permission.
     *
     * @param   string $role
     * @param   string $permission
     *
     * @return  bool
     */
    public function isRoleGranted(string $role, string $permission)
    {
        $filtered = $this->rolePermissions->filter(function (TemplateRolePermission $rolePermission) use ($role, $permission) {
            return $rolePermission->getRole() === $role && $rolePermission->getPermission() === $permission;
        });

        return count($filtered) !== 0;
    }

    /**
     * Checks whether current user is granted for specified permission (as a member of at least one of allowed groups).
     *
     * @param   CurrentUser $user
     * @param   string      $permission
     *
     * @return  bool
     */
    public function isUserGranted(CurrentUser $user, string $permission)
    {
        if ($this->currentUser !== $user->getId()) {

            $this->currentUser = $user->getId();

            $builder = $this->manager->createQueryBuilder();

            $query = $builder
                ->select('groupPermission')
                ->from(TemplateGroupPermission::class, 'groupPermission')
                ->leftJoin('groupPermission.group', 'group')
                ->where('groupPermission.template = :template')
                ->andWhere($builder->expr()->isMemberOf(':user', 'group.members'))
            ;

            $query->setParameters([
                'template' => $this,
                'user'     => $user->getId(),
            ]);

            $this->userPermissions = array_map(function (TemplateGroupPermission $permission) {
                return $permission->getPermission();
            }, $query->getQuery()->getResult());
        }

        return in_array($permission, $this->userPermissions);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return 'template#' . $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id'          => $this->getId(),
            'project'     => $this->getProject()->getId(),
            'name'        => $this->getName(),
            'prefix'      => $this->getPrefix(),
            'criticalAge' => $this->getCriticalAge(),
            'frozenTime'  => $this->getFrozenTime(),
            'description' => $this->getDescription(),
            'isLocked'    => $this->isLocked(),
        ];
    }
}
