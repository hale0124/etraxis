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
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

/**
 * Field.
 *
 * @ORM\Table(name="fields",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_fields_name", columns={"state_id", "name", "removed_at"}),
 *                @ORM\UniqueConstraint(name="ix_fields_order", columns={"state_id", "field_order", "removed_at"})
 *            })
 * @ORM\Entity
 * @ORM\EntityListeners({"eTraxis\Entity\EntityListener"})
 * @Assert\UniqueEntity(fields={"state", "name", "removedAt"}, message="field.conflict.name", ignoreNull=false)
 */
class Field extends Entity implements \JsonSerializable
{
    // Constraints.
    const MAX_NAME        = 50;
    const MAX_DESCRIPTION = 1000;

    // Actions.
    const DELETE = 'field.delete';

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var State State of the field (NULL in case of global field).
     *
     * @ORM\ManyToOne(targetEntity="State", inversedBy="fields")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $state;

    /**
     * @var string Name of the field.
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string Type of the field.
     *
     * @ORM\Column(name="type", type="string", length=10)
     */
    private $type;

    /**
     * @var string Optional description of the field.
     *
     * @ORM\Column(name="description", type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * @var int Ordinal number of the field. No duplicates of this number among fields of the same state are allowed.
     *
     * @ORM\Column(name="field_order", type="integer")
     */
    private $order;

    /**
     * @var int Unix Epoch timestamp when the field has been removed ("NULL" while field is present).
     *
     * @ORM\Column(name="removed_at", type="integer", nullable=true)
     */
    private $removedAt;

    /**
     * @var bool Whether the field is required.
     *
     * @ORM\Column(name="is_required", type="boolean")
     */
    private $isRequired;

    /**
     * @var FieldPCRE Perl-compatible regular expression options.
     *
     * @ORM\Embedded(class="FieldPCRE")
     */
    private $pcre;

    /**
     * @var FieldParameters Field type-specific parameters.
     *
     * @ORM\Embedded(class="FieldParameters", columnPrefix=false)
     */
    private $parameters;

    /**
     * @var ArrayCollection List of role permissions.
     *
     * @ORM\OneToMany(targetEntity="FieldRolePermission", mappedBy="field", cascade={"persist"})
     */
    private $rolePermissions;

    /**
     * @var ArrayCollection List of group permissions.
     *
     * @ORM\OneToMany(targetEntity="FieldGroupPermission", mappedBy="field", cascade={"persist"})
     */
    private $groupPermissions;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->pcre       = new FieldPCRE();
        $this->parameters = new FieldParameters();

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
     * Property setter.
     *
     * @param   State $state
     *
     * @return  self
     */
    public function setState(State $state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  State
     */
    public function getState()
    {
        return $this->state;
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
     * @param   string $type
     *
     * @return  self
     */
    public function setType(string $type)
    {
        if (Dictionary\FieldType::has($type)) {
            $this->type = $type;
        }

        return $this;
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
     * @param   int $order
     *
     * @return  self
     */
    public function setOrder(int $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Removes (deletes softly) the field.
     *
     * @return  self
     */
    public function remove()
    {
        $this->removedAt = time();
        $this->order     = 0;

        return $this;
    }

    /**
     * Checks whether the field is removed (soft-deleted).
     *
     * @return  bool
     */
    public function isRemoved()
    {
        return $this->removedAt !== null;
    }

    /**
     * Property setter.
     *
     * @param   bool $isRequired
     *
     * @return  self
     */
    public function setRequired(bool $isRequired)
    {
        $this->isRequired = $isRequired;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  bool
     */
    public function isRequired()
    {
        return $this->isRequired;
    }

    /**
     * Property getter.
     *
     * @return  FieldPCRE
     */
    public function getPCRE()
    {
        return $this->pcre;
    }

    /**
     * Property getter.
     *
     * @return  FieldParameters
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets permission of specified system role.
     *
     * @param   string $role
     * @param   string $permission
     *
     * @return  self
     */
    public function setRolePermission(string $role, string $permission)
    {
        if (Dictionary\FieldPermission::has($permission)) {

            // Filter all permissions by the role.
            $permissions = $this->rolePermissions->filter(function (FieldRolePermission $permission) use ($role) {
                return $permission->getRole() === $role;
            });

            // Retrieve the permission part.
            $current = $permissions->map(function (FieldRolePermission $permission) {
                return $permission->getPermission();
            });

            $desired = [];

            if ($permission === Dictionary\FieldPermission::READ_ONLY) {
                $desired[] = Dictionary\FieldPermission::READ_ONLY;
            }

            if ($permission === Dictionary\FieldPermission::READ_WRITE) {
                $desired[] = Dictionary\FieldPermission::READ_ONLY;
                $desired[] = Dictionary\FieldPermission::READ_WRITE;
            }

            $toAdd = array_unique(array_diff($desired, $current->toArray()));

            // Remove extra transitions.
            foreach ($this->rolePermissions as $key => $item) {
                /** @var FieldRolePermission $item */
                if ($item->getRole() === $role) {
                    if (!in_array($item->getPermission(), $desired)) {
                        $this->rolePermissions->remove($key);
                        $this->manager->remove($item);
                    }
                }
            }

            // Grant required transitions.
            foreach ($toAdd as $item) {

                $element = new FieldRolePermission();

                $element
                    ->setField($this)
                    ->setRole($role)
                    ->setPermission($item)
                ;

                $this->rolePermissions->add($element);
            }
        }

        return $this;
    }

    /**
     * Returns permission of specified system role.
     *
     * @param   string $role
     *
     * @return  string
     */
    public function getRolePermission(string $role)
    {
        // Filter all permissions by the role.
        $permissions = $this->rolePermissions->filter(function (FieldRolePermission $permission) use ($role) {
            return $permission->getRole() === $role;
        });

        // Retrieve the permission part.
        $filtered = $permissions->map(function (FieldRolePermission $permission) {
            return $permission->getPermission();
        });

        if ($filtered->contains(Dictionary\FieldPermission::READ_WRITE)) {
            return Dictionary\FieldPermission::READ_WRITE;
        }

        if ($filtered->contains(Dictionary\FieldPermission::READ_ONLY)) {
            return Dictionary\FieldPermission::READ_ONLY;
        }

        return Dictionary\FieldPermission::NONE;
    }

    /**
     * Sets permission of specified group.
     *
     * @param   Group  $group
     * @param   string $permission
     *
     * @return  self
     */
    public function setGroupPermission(Group $group, string $permission)
    {
        if (Dictionary\FieldPermission::has($permission)) {

            // Filter all permissions by the group.
            $permissions = $this->groupPermissions->filter(function (FieldGroupPermission $permission) use ($group) {
                return $permission->getGroup() === $group;
            });

            // Retrieve the permission part.
            $current = $permissions->map(function (FieldGroupPermission $permission) {
                return $permission->getPermission();
            });

            $desired = [];

            if ($permission === Dictionary\FieldPermission::READ_ONLY) {
                $desired[] = Dictionary\FieldPermission::READ_ONLY;
            }

            if ($permission === Dictionary\FieldPermission::READ_WRITE) {
                $desired[] = Dictionary\FieldPermission::READ_ONLY;
                $desired[] = Dictionary\FieldPermission::READ_WRITE;
            }

            $toAdd = array_unique(array_diff($desired, $current->toArray()));

            // Remove extra transitions.
            foreach ($this->groupPermissions as $key => $item) {
                /** @var FieldGroupPermission $item */
                if ($item->getGroup() === $group) {
                    if (!in_array($item->getPermission(), $desired)) {
                        $this->groupPermissions->remove($key);
                        $this->manager->remove($item);
                    }
                }
            }

            // Grant required transitions.
            foreach ($toAdd as $item) {

                $element = new FieldGroupPermission();

                $element
                    ->setField($this)
                    ->setGroup($group)
                    ->setPermission($item)
                ;

                $this->groupPermissions->add($element);
            }
        }

        return $this;
    }

    /**
     * Returns permission of specified group.
     *
     * @param   Group $group
     *
     * @return  string
     */
    public function getGroupPermission(Group $group)
    {
        // Filter all permissions by the group.
        $permissions = $this->groupPermissions->filter(function (FieldGroupPermission $permission) use ($group) {
            return $permission->getGroup() === $group;
        });

        // Retrieve the permission part.
        $filtered = $permissions->map(function (FieldGroupPermission $permission) {
            return $permission->getPermission();
        });

        if ($filtered->contains(Dictionary\FieldPermission::READ_WRITE)) {
            return Dictionary\FieldPermission::READ_WRITE;
        }

        if ($filtered->contains(Dictionary\FieldPermission::READ_ONLY)) {
            return Dictionary\FieldPermission::READ_ONLY;
        }

        return Dictionary\FieldPermission::NONE;
    }

    /**
     * Returns facade for "number" field.
     *
     * @return  Fields\NumberField
     */
    public function asNumber()
    {
        return new Fields\NumberField($this);
    }

    /**
     * Returns facade for "decimal" field.
     *
     * @return  Fields\DecimalField
     */
    public function asDecimal()
    {
        return new Fields\DecimalField($this, $this->manager->getRepository(DecimalValue::class));
    }

    /**
     * Returns facade for "string" field.
     *
     * @return  Fields\StringField
     */
    public function asString()
    {
        return new Fields\StringField($this, $this->manager->getRepository(StringValue::class));
    }

    /**
     * Returns facade for "text" field.
     *
     * @return  Fields\TextField
     */
    public function asText()
    {
        return new Fields\TextField($this, $this->manager->getRepository(TextValue::class));
    }

    /**
     * Returns facade for "checkbox" field.
     *
     * @return  Fields\CheckboxField
     */
    public function asCheckbox()
    {
        return new Fields\CheckboxField($this);
    }

    /**
     * Returns facade for "list" field.
     *
     * @return  Fields\ListField
     */
    public function asList()
    {
        return new Fields\ListField($this, $this->manager->getRepository(ListItem::class));
    }

    /**
     * Returns facade for "record" field.
     *
     * @return  Fields\RecordField
     */
    public function asRecord()
    {
        return new Fields\RecordField($this);
    }

    /**
     * Returns facade for "date" field.
     *
     * @return  Fields\DateField
     */
    public function asDate()
    {
        return new Fields\DateField($this);
    }

    /**
     * Returns facade for "duration" field.
     *
     * @return  Fields\DurationField
     */
    public function asDuration()
    {
        return new Fields\DurationField($this);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return 'field#' . $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id'          => $this->getId(),
            'state'       => $this->getState()->getId(),
            'name'        => $this->getName(),
            'type'        => $this->getType(),
            'description' => $this->getDescription(),
            'order'       => $this->getOrder(),
            'isRequired'  => $this->isRequired(),
        ];
    }
}
