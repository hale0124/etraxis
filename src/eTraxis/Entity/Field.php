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
use eTraxis\Repository\DecimalValuesRepository;
use eTraxis\Repository\ListItemsRepository;
use eTraxis\Repository\StringValuesRepository;
use eTraxis\Repository\TextValuesRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

/**
 * Field.
 *
 * @ORM\Table(name="tbl_fields",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_fields_name", columns={"state_id", "field_name", "removal_time"}),
 *                @ORM\UniqueConstraint(name="ix_fields_order", columns={"state_id", "field_order", "removal_time"})
 *            })
 * @ORM\Entity(repositoryClass="eTraxis\Repository\FieldsRepository")
 * @ORM\EntityListeners({"eTraxis\Entity\Fields\FieldListener"})
 * @Assert\UniqueEntity(fields={"template", "state", "name", "removedAt"}, message="field.conflict.name", ignoreNull=false)
 */
class Field
{
    // Constraints.
    const MAX_NAME        = 50;
    const MAX_DESCRIPTION = 1000;

    // Actions.
    const DELETE = 'field.delete';

    // Field type.
    const TYPE_NUMBER   = 1;
    const TYPE_STRING   = 2;
    const TYPE_TEXT     = 3;
    const TYPE_CHECKBOX = 4;
    const TYPE_LIST     = 5;
    const TYPE_RECORD   = 6;
    const TYPE_DATE     = 7;
    const TYPE_DURATION = 8;
    const TYPE_DECIMAL  = 9;

    // Field access.
    const ACCESS_DENIED     = 0;
    const ACCESS_READ_ONLY  = 1;
    const ACCESS_READ_WRITE = 2;

    // Repositories.
    protected $decimalValues;
    protected $stringValues;
    protected $textValues;
    protected $listItems;

    /**
     * @var int Unique ID.
     *
     * @ORM\Column(name="field_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int Template ID of the field.
     *
     * @ORM\Column(name="template_id", type="integer")
     */
    private $templateId;

    /**
     * @var int State ID of the field (NULL in case of global field).
     *
     * @ORM\Column(name="state_id", type="integer", nullable=true)
     */
    private $stateId;

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
     * @var int Whether the field is accessible for non-authenticated user.
     *
     * @ORM\Column(name="guest_access", type="integer")
     */
    private $hasGuestAccess;

    /**
     * @var int Access level for authenticated user.
     *
     * @ORM\Column(name="registered_perm", type="integer")
     */
    private $registeredAccess;

    /**
     * @var int Access level for author.
     *
     * @ORM\Column(name="author_perm", type="integer")
     */
    private $authorAccess;

    /**
     * @var int Access level for current responsible.
     *
     * @ORM\Column(name="responsible_perm", type="integer")
     */
    private $responsibleAccess;

    /**
     * @deprecated 4.1.0
     * @ORM\Column(name="add_separator", type="integer")
     */
    private $addSeparator;

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
     * @var Template Template of the field.
     *
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="fields")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="template_id", onDelete="CASCADE")
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
     * Constructor.
     */
    public function __construct()
    {
        $this->removedAt    = 0;
        $this->addSeparator = 0;

        $this->regex      = new FieldRegex();
        $this->parameters = new FieldParameters();
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
     * @param   int $templateId
     *
     * @return  self
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * Standard setter.
     *
     * @param   int $stateId
     *
     * @return  self
     */
    public function setStateId($stateId)
    {
        $this->stateId = $stateId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getStateId()
    {
        return $this->stateId;
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
     * @param   int $type
     *
     * @return  self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getType()
    {
        return $this->type;
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
     * @param   int $indexNumber
     *
     * @return  self
     */
    public function setIndexNumber($indexNumber)
    {
        $this->indexNumber = $indexNumber;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getIndexNumber()
    {
        return $this->indexNumber;
    }

    /**
     * Standard setter.
     *
     * @param   int $removedAt
     *
     * @return  self
     */
    public function setRemovedAt($removedAt)
    {
        $this->removedAt = $removedAt;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getRemovedAt()
    {
        return $this->removedAt;
    }

    /**
     * Standard setter.
     *
     * @param   bool $isRequired
     *
     * @return  self
     */
    public function setRequired($isRequired)
    {
        $this->isRequired = $isRequired ? 1 : 0;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  bool
     */
    public function isRequired()
    {
        return (bool) $this->isRequired;
    }

    /**
     * Standard setter.
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
     * Standard getter.
     *
     * @return  bool
     */
    public function hasGuestAccess()
    {
        return (bool) $this->hasGuestAccess;
    }

    /**
     * Standard setter.
     *
     * @param   int $registeredAccess
     *
     * @return  self
     */
    public function setRegisteredAccess($registeredAccess)
    {
        $this->registeredAccess = $registeredAccess;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getRegisteredAccess()
    {
        return $this->registeredAccess;
    }

    /**
     * Standard setter.
     *
     * @param   int $authorAccess
     *
     * @return  self
     */
    public function setAuthorAccess($authorAccess)
    {
        $this->authorAccess = $authorAccess;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getAuthorAccess()
    {
        return $this->authorAccess;
    }

    /**
     * Standard setter.
     *
     * @param   int $responsibleAccess
     *
     * @return  self
     */
    public function setResponsibleAccess($responsibleAccess)
    {
        $this->responsibleAccess = $responsibleAccess;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getResponsibleAccess()
    {
        return $this->responsibleAccess;
    }

    /**
     * Standard setter.
     *
     * @param   bool $showInEmails
     *
     * @return  self
     */
    public function setShowInEmails($showInEmails)
    {
        $this->showInEmails = $showInEmails ? 1 : 0;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  bool
     */
    public function getShowInEmails()
    {
        return (bool) $this->showInEmails;
    }

    /**
     * Standard getter.
     *
     * @return  FieldRegex
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * Standard getter.
     *
     * @return  FieldParameters
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Standard setter.
     *
     * @param   Template $template
     *
     * @return  self
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Standard setter.
     *
     * @param   State $state
     *
     * @return  self
     */
    public function setState(State $state = null)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Returns internal name of the type.
     *
     * @return  string
     *
     * @todo    Remove when switched from numeric type IDs to string ones.
     */
    public function getTypeEx()
    {
        $types = [
            self::TYPE_NUMBER   => 'number',
            self::TYPE_DECIMAL  => 'decimal',
            self::TYPE_STRING   => 'string',
            self::TYPE_TEXT     => 'text',
            self::TYPE_CHECKBOX => 'checkbox',
            self::TYPE_LIST     => 'list',
            self::TYPE_RECORD   => 'record',
            self::TYPE_DATE     => 'date',
            self::TYPE_DURATION => 'duration',
        ];

        if (!array_key_exists($this->type, $types)) {
            return null;
        }

        return $types[$this->type];
    }

    /**
     * Dependency Injection setter.
     *
     * @param   DecimalValuesRepository $repository
     *
     * @return  self
     */
    public function setDecimalValuesRepository(DecimalValuesRepository $repository)
    {
        $this->decimalValues = $repository;

        return $this;
    }

    /**
     * Dependency Injection setter.
     *
     * @param   StringValuesRepository $repository
     *
     * @return  self
     */
    public function setStringValuesRepository(StringValuesRepository $repository)
    {
        $this->stringValues = $repository;

        return $this;
    }

    /**
     * Dependency Injection setter.
     *
     * @param   TextValuesRepository $repository
     *
     * @return  self
     */
    public function setTextValuesRepository(TextValuesRepository $repository)
    {
        $this->textValues = $repository;

        return $this;
    }

    /**
     * Dependency Injection setter.
     *
     * @param   ListItemsRepository $repository
     *
     * @return  self
     */
    public function setListItemsRepository(ListItemsRepository $repository)
    {
        $this->listItems = $repository;

        return $this;
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
        return new Fields\DecimalField($this, $this->decimalValues);
    }

    /**
     * Returns facade for "string" field.
     *
     * @return  Fields\StringField
     */
    public function asString()
    {
        return new Fields\StringField($this, $this->stringValues);
    }

    /**
     * Returns facade for "text" field.
     *
     * @return  Fields\TextField
     */
    public function asText()
    {
        return new Fields\TextField($this, $this->textValues);
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
        return new Fields\ListField($this, $this->listItems);
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
}
