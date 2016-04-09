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
 * Template.
 *
 * @ORM\Table(name="tbl_templates",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_templates_name", columns={"project_id", "template_name"}),
 *                @ORM\UniqueConstraint(name="ix_templates_prefix", columns={"project_id", "template_prefix"})
 *            })
 * @ORM\Entity(repositoryClass="eTraxis\Repository\TemplatesRepository")
 * @Assert\UniqueEntity(fields={"project", "name"}, message="template.conflict.name")
 * @Assert\UniqueEntity(fields={"project", "prefix"}, message="template.conflict.prefix")
 */
class Template implements \JsonSerializable
{
    // Constraints.
    const MAX_NAME        = 50;
    const MAX_PREFIX      = 3;
    const MAX_DESCRIPTION = 100;

    // Actions.
    const DELETE = 'template.delete';
    const LOCK   = 'template.lock';
    const UNLOCK = 'template.unlock';

    // Template access permissions.
    const PERMIT_CREATE_RECORD    = 0x0001;
    const PERMIT_EDIT_RECORD      = 0x0002;
    const PERMIT_POSTPONE_RECORD  = 0x0004;
    const PERMIT_RESUME_RECORD    = 0x0008;
    const PERMIT_REASSIGN_RECORD  = 0x0010;
    const PERMIT_REOPEN_RECORD    = 0x0020;
    const PERMIT_ADD_COMMENT      = 0x0040;
    const PERMIT_ADD_FILE         = 0x0080;
    const PERMIT_REMOVE_FILE      = 0x0100;
    const PERMIT_PRIVATE_COMMENT  = 0x0200;
    const PERMIT_SEND_REMINDER    = 0x0400;
    const PERMIT_DELETE_RECORD    = 0x0800;
    const PERMIT_ATTACH_SUBRECORD = 0x1000;
    const PERMIT_DETACH_SUBRECORD = 0x2000;
    const PERMIT_VIEW_RECORD      = 0x40000000;

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="template_id", type="integer")
     */
    private $id;

    /**
     * @var Project Project of the template.
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="templates")
     * @ORM\JoinColumn(name="project_id", nullable=false, referencedColumnName="project_id", onDelete="CASCADE")
     */
    private $project;

    /**
     * @var string Name of the template.
     *
     * @ORM\Column(name="template_name", type="string", length=50)
     */
    private $name;

    /**
     * @var string Prefix of the template (used as a prefix in ID of records, created using this template).
     *
     * @ORM\Column(name="template_prefix", type="string", length=3)
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
     * @var int Whether the template is locked for edition.
     *
     * @ORM\Column(name="is_locked", type="integer")
     */
    private $isLocked;

    /**
     * @var int Whether a record, created using this template, is accessible for non-authenticated user.
     *
     * @ORM\Column(name="guest_access", type="integer")
     */
    private $hasGuestAccess;

    /**
     * @var int Authenticated user permissions.
     *
     * @ORM\Column(name="registered_perm", type="integer")
     */
    private $registeredPermissions;

    /**
     * @var int Author permissions.
     *
     * @ORM\Column(name="author_perm", type="integer")
     */
    private $authorPermissions;

    /**
     * @var int Current responsible permissions.
     *
     * @ORM\Column(name="responsible_perm", type="integer")
     */
    private $responsiblePermissions;

    /**
     * @var ArrayCollection List of template states.
     *
     * @ORM\OneToMany(targetEntity="State", mappedBy="template")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $states;

    /**
     * @var ArrayCollection List of template fields.
     *
     * @ORM\OneToMany(targetEntity="Field", mappedBy="template")
     * @ORM\OrderBy({"indexNumber" = "ASC"})
     */
    private $fields;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->states = new ArrayCollection();
        $this->fields = new ArrayCollection();
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
    public function setName($name)
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
    public function setPrefix($prefix)
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
     * @param   int $criticalAge
     *
     * @return  self
     */
    public function setCriticalAge($criticalAge)
    {
        $this->criticalAge = $criticalAge;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getCriticalAge()
    {
        return $this->criticalAge;
    }

    /**
     * Property setter.
     *
     * @param   int $frozenTime
     *
     * @return  self
     */
    public function setFrozenTime($frozenTime)
    {
        $this->frozenTime = $frozenTime;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getFrozenTime()
    {
        return $this->frozenTime;
    }

    /**
     * Property setter.
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
     * Property getter.
     *
     * @return  string
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
    public function setLocked($isLocked)
    {
        $this->isLocked = $isLocked ? 1 : 0;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  bool
     */
    public function isLocked()
    {
        return (bool) $this->isLocked;
    }

    /**
     * Property setter.
     *
     * @param   bool $hasGuestAccess
     *
     * @return  self
     */
    public function setGuestAccess($hasGuestAccess)
    {
        $this->hasGuestAccess = $hasGuestAccess ? 1 : 0;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  bool
     */
    public function hasGuestAccess()
    {
        return (bool) $this->hasGuestAccess;
    }

    /**
     * Property setter.
     *
     * @param   int $registeredPermissions
     *
     * @return  self
     */
    public function setRegisteredPermissions($registeredPermissions)
    {
        $this->registeredPermissions = $registeredPermissions;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getRegisteredPermissions()
    {
        return $this->registeredPermissions;
    }

    /**
     * Property setter.
     *
     * @param   int $authorPermissions
     *
     * @return  self
     */
    public function setAuthorPermissions($authorPermissions)
    {
        $this->authorPermissions = $authorPermissions;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getAuthorPermissions()
    {
        return $this->authorPermissions;
    }

    /**
     * Property setter.
     *
     * @param   int $responsiblePermissions
     *
     * @return  self
     */
    public function setResponsiblePermissions($responsiblePermissions)
    {
        $this->responsiblePermissions = $responsiblePermissions;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getResponsiblePermissions()
    {
        return $this->responsiblePermissions;
    }

    /**
     * Property setter.
     *
     * @param   Project $project
     *
     * @return  self
     */
    public function setProject(Project $project)
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
     * Add state to the template.
     *
     * @param   State $state
     *
     * @return  self
     */
    public function addState(State $state)
    {
        $this->states[] = $state;

        return $this;
    }

    /**
     * Remove state from the template.
     *
     * @param   State $state
     *
     * @return  self
     */
    public function removeState(State $state)
    {
        $this->states->removeElement($state);

        return $this;
    }

    /**
     * Get list of template states.
     *
     * @return  State[]
     */
    public function getStates()
    {
        return $this->states->toArray();
    }

    /**
     * Add field to the template.
     *
     * @param   Field $field
     *
     * @return  self
     */
    public function addField(Field $field)
    {
        $this->fields[] = $field;

        return $this;
    }

    /**
     * Remove field from the template.
     *
     * @param   Field $field
     *
     * @return  self
     */
    public function removeField(Field $field)
    {
        $this->fields->removeElement($field);

        return $this;
    }

    /**
     * Get list of template fields.
     *
     * @return  Field[]
     */
    public function getFields()
    {
        return $this->fields->filter(function (Field $field) {
            return $field->getState() === null;
        })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'prefix'      => $this->prefix,
            'criticalAge' => $this->criticalAge,
            'frozenTime'  => $this->frozenTime,
            'description' => $this->description,
            'isLocked'    => $this->isLocked,
        ];
    }
}
