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
 * @Assert\UniqueEntity(fields={"template", "state", "name", "removedAt"}, message="field.conflict.name", ignoreNull=false)
 */
class Field
{
    // Constraints.
    const MAX_NAME        = 50;
    const MAX_DESCRIPTION = 1000;
    const MAX_REGEX       = 500;

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
     * @var string Perl-compatible regular expression which values of the field must conform to.
     *
     * @ORM\Column(name="regex_check", type="string", length=500, nullable=true)
     */
    private $regexCheck;

    /**
     * @var string Perl-compatible regular expression to modify values of the field before display them (search for).
     *
     * @ORM\Column(name="regex_search", type="string", length=500, nullable=true)
     */
    private $regexSearch;

    /**
     * @var string Perl-compatible regular expression to modify values of the field before display them (replace with).
     *
     * @ORM\Column(name="regex_replace", type="string", length=500, nullable=true)
     */
    private $regexReplace;

    /**
     * @var int First parameter of the field. Depends on field type as following:
     *
     *          "number"   - minimum of range of allowed values (from -1000000000 till +1000000000)
     *          "decimal"  - minimum of range of allowed values (foreign key to "decimal_values" table)
     *          "string"   - maximum allowed length of values (up to 250)
     *          "text"     - maximum allowed length of values (up to 4000)
     *          "checkbox" - NULL (not used)
     *          "list"     - NULL (not used)
     *          "record"   - NULL (not used)
     *          "date"     - minimum of range of allowed values (amount of days since current date; negative value shifts to the past)
     *          "duration" - minimum of range of allowed values (amount of minutes from 0:00 till 999999:59)
     *
     * @ORM\Column(name="param1", type="integer", nullable=true)
     */
    private $parameter1;

    /**
     * @var int Second parameter of the field. Depends on field type as following:
     *
     *          "number"   - maximum of range of allowed values (from -1000000000 till +1000000000)
     *          "decimal"  - maximum of range of allowed values (foreign key to "decimal_values" table)
     *          "string"   - NULL (not used)
     *          "text"     - NULL (not used)
     *          "checkbox" - NULL (not used)
     *          "list"     - NULL (not used)
     *          "record"   - NULL (not used)
     *          "date"     - maximum of range of allowed values (amount of days since current date; negative value shifts to the past)
     *          "duration" - maximum of range of allowed values (amount of minutes from 0:00 till 999999:59)
     *
     * @ORM\Column(name="param2", type="integer", nullable=true)
     */
    private $parameter2;

    /**
     * @var int Default value of the field. Depends on field type as following:
     *
     *          "number"   - default integer value (from -1000000000 till +1000000000)
     *          "decimal"  - default decimal value (foreign key to "decimal_values" table)
     *          "string"   - default string value (foreign key to "string_values" table)
     *          "text"     - default string value (foreign key to "text_values" table)
     *          "checkbox" - default state of checkbox (0 - unchecked, 1 - checked)
     *          "list"     - integer value of default list item (see "list_values" table)
     *          "record"   - NULL (not used)
     *          "date"     - default date value (amount of days since current date; negative value shifts to the past)
     *          "duration" - default duration value (amount of minutes from 0:00 till 999999:59)
     *
     * @ORM\Column(name="value_id", type="integer", nullable=true)
     */
    private $defaultValue;

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
     * Standard setter.
     *
     * @param   string $regexCheck
     *
     * @return  self
     */
    public function setRegexCheck($regexCheck)
    {
        $this->regexCheck = $regexCheck;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getRegexCheck()
    {
        return $this->regexCheck;
    }

    /**
     * Standard setter.
     *
     * @param   string $regexSearch
     *
     * @return  self
     */
    public function setRegexSearch($regexSearch)
    {
        $this->regexSearch = $regexSearch;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getRegexSearch()
    {
        return $this->regexSearch;
    }

    /**
     * Standard setter.
     *
     * @param   string $regexReplace
     *
     * @return  self
     */
    public function setRegexReplace($regexReplace)
    {
        $this->regexReplace = $regexReplace;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getRegexReplace()
    {
        return $this->regexReplace;
    }

    /**
     * Standard setter.
     *
     * @param   int $parameter1
     *
     * @return  self
     */
    public function setParameter1($parameter1)
    {
        $this->parameter1 = $parameter1;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getParameter1()
    {
        return $this->parameter1;
    }

    /**
     * Standard setter.
     *
     * @param   int $parameter2
     *
     * @return  self
     */
    public function setParameter2($parameter2)
    {
        $this->parameter2 = $parameter2;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getParameter2()
    {
        return $this->parameter2;
    }

    /**
     * Standard setter.
     *
     * @param   int $defaultValue
     *
     * @return  self
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
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
}
