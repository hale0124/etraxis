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
use eTraxis\Dictionary;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

/**
 * Field.
 *
 * @ORM\Table(name="tbl_fields",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_fields_name", columns={"state_id", "field_name", "removal_time"}),
 *                @ORM\UniqueConstraint(name="ix_fields_order", columns={"state_id", "field_order", "removal_time"})
 *            })
 * @ORM\Entity
 * @ORM\EntityListeners({"eTraxis\Entity\EntityListener"})
 * @Assert\UniqueEntity(fields={"template", "state", "name", "removedAt"}, message="field.conflict.name", ignoreNull=false)
 */
class Field extends Entity implements \JsonSerializable
{
    // Constraints.
    const MAX_NAME        = 50;
    const MAX_DESCRIPTION = 1000;

    // Actions.
    const DELETE = 'field.delete';

    // Field type.
    const TYPE_NUMBER   = 'number';
    const TYPE_DECIMAL  = 'decimal';
    const TYPE_STRING   = 'string';
    const TYPE_TEXT     = 'text';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_LIST     = 'list';
    const TYPE_RECORD   = 'record';
    const TYPE_DATE     = 'date';
    const TYPE_DURATION = 'duration';

    // Field permission.
    const ACCESS_DENIED     = 0;
    const ACCESS_READ_ONLY  = 1;
    const ACCESS_READ_WRITE = 2;

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="field_id", type="integer")
     */
    private $id;

    /**
     * @var Template Template of the field.
     *
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="fields")
     * @ORM\JoinColumn(name="template_id", nullable=false, referencedColumnName="template_id", onDelete="CASCADE")
     */
    private $template;

    /**
     * @var State State of the field (NULL in case of global field).
     *
     * @ORM\ManyToOne(targetEntity="State", inversedBy="fields")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="state_id", onDelete="CASCADE")
     */
    private $state;

    /**
     * @var string Name of the field.
     *
     * @ORM\Column(name="field_name", type="string", length=50)
     */
    private $name;

    /**
     * @var int Type of the field.
     *
     * @ORM\Column(name="field_type", type="integer")
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
    private $indexNumber;

    /**
     * @var int Unix Epoch timestamp when the field has been removed from its template ("0" while field is present).
     *
     * @ORM\Column(name="removal_time", type="integer")
     */
    private $removedAt;

    /**
     * @var int Whether the field is required.
     *
     * @ORM\Column(name="is_required", type="integer")
     */
    private $isRequired;

    /**
     * @var int Permission for author.
     *
     * @ORM\Column(name="author_perm", type="integer")
     */
    private $authorPermission;

    /**
     * @var int Permission for current responsible.
     *
     * @ORM\Column(name="responsible_perm", type="integer")
     */
    private $responsiblePermission;

    /**
     * @var int Permission for authenticated user.
     *
     * @ORM\Column(name="registered_perm", type="integer")
     */
    private $registeredPermission;

    /**
     * @var int Whether to add this field in email notifications.
     *
     * @ORM\Column(name="show_in_emails", type="integer")
     */
    private $showInEmails;

    /**
     * @var FieldRegex Perl-compatible regular expression options.
     *
     * @ORM\Embedded(class="FieldRegex")
     */
    private $regex;

    /**
     * @var FieldParameters Field type-specific parameters.
     *
     * @ORM\Embedded(class="FieldParameters", columnPrefix=false)
     */
    private $parameters;

    /**
     * @var FieldDeprecated Deprecated features.
     *
     * @ORM\Embedded(class="FieldDeprecated", columnPrefix=false)
     */
    private $deprecated;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->removedAt = 0;

        $this->regex      = new FieldRegex();
        $this->parameters = new FieldParameters();
        $this->deprecated = new FieldDeprecated();
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
        $this->state    = $state;
        $this->template = $state->getTemplate();

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
        $types = array_flip(Dictionary\LegacyFieldType::all());

        if (array_key_exists($type, $types)) {
            $this->type = $types[$type];
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
        return Dictionary\LegacyFieldType::get($this->type);
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
     * @param   int $indexNumber
     *
     * @return  self
     */
    public function setIndexNumber(int $indexNumber)
    {
        $this->indexNumber = $indexNumber;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getIndexNumber()
    {
        return $this->indexNumber;
    }

    /**
     * Removes (deletes softly) the field.
     *
     * @return  self
     */
    public function remove()
    {
        $this->removedAt   = time();
        $this->indexNumber = 0;

        return $this;
    }

    /**
     * Checks whether the field is removed (soft-deleted).
     *
     * @return  bool
     */
    public function isRemoved()
    {
        return $this->removedAt !== 0;
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
        $this->isRequired = $isRequired ? 1 : 0;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  bool
     */
    public function isRequired()
    {
        return (bool) $this->isRequired;
    }

    /**
     * Sets permission of specified system role.
     *
     * @param   int $role
     * @param   int $permission
     *
     * @return  self
     */
    public function setRolePermission(int $role, int $permission)
    {
        switch ($role) {

            case Dictionary\SystemRole::AUTHOR:
                $this->authorPermission = $permission;
                break;

            case Dictionary\SystemRole::RESPONSIBLE:
                $this->responsiblePermission = $permission;
                break;

            case Dictionary\SystemRole::REGISTERED:
                $this->registeredPermission = $permission;
                break;
        }

        return $this;
    }

    /**
     * Returns permission of specified system role.
     *
     * @param   int $role
     *
     * @return  int
     */
    public function getRolePermission(int $role)
    {
        switch ($role) {

            case Dictionary\SystemRole::AUTHOR:
                return $this->authorPermission;

            case Dictionary\SystemRole::RESPONSIBLE:
                return $this->responsiblePermission;

            case Dictionary\SystemRole::REGISTERED:
                return $this->registeredPermission;
        }

        return self::ACCESS_DENIED;
    }

    /**
     * Returns permission of specified group.
     *
     * @param   Group $group
     *
     * @return  int
     *
     * @todo    Refactor into single database entry.
     */
    public function getGroupPermission(Group $group)
    {
        $query = $this->manager->createQueryBuilder();

        $query
            ->select('fgp.permission')
            ->from(FieldGroupPermission::class, 'fgp')
            ->where('fgp.field = :field')
            ->andWhere('fgp.group = :group')
            ->setParameter('field', $this)
            ->setParameter('group', $group)
        ;

        $result     = $query->getQuery()->getResult();
        $result[]   = ['permission' => self::ACCESS_DENIED];
        $permission = max($result);

        return count($result) === 0 ? self::ACCESS_DENIED : $permission['permission'];
    }

    /**
     * Property setter.
     *
     * @param   bool $showInEmails
     *
     * @return  self
     */
    public function setShowInEmails(bool $showInEmails)
    {
        $this->showInEmails = $showInEmails ? 1 : 0;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  bool
     */
    public function getShowInEmails()
    {
        return (bool) $this->showInEmails;
    }

    /**
     * Property getter.
     *
     * @return  FieldRegex
     */
    public function getRegex()
    {
        return $this->regex;
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
    public function jsonSerialize()
    {
        return [
            'id'          => $this->getId(),
            'name'        => $this->getName(),
            'type'        => $this->getType(),
            'description' => $this->getDescription(),
            'isRequired'  => $this->isRequired(),
        ];
    }
}
