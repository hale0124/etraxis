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

namespace eTraxis\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * State.
 *
 * @ORM\Table(name="tbl_states",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_states_name", columns={"template_id", "state_name"}),
 *                @ORM\UniqueConstraint(name="ix_states_abbr", columns={"template_id", "state_abbr"})
 *            })
 * @ORM\Entity
 */
class State
{
    // State types.
    const TYPE_INITIAL   = 1;
    const TYPE_TRANSIENT = 2;
    const TYPE_FINAL     = 3;

    // State responsibility management.
    const RESPONSIBLE_KEEP   = 1;
    const RESPONSIBLE_ASSIGN = 2;
    const RESPONSIBLE_REMOVE = 3;

    /**
     * @var int Unique ID.
     *
     * @ORM\Column(name="state_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int Template ID of the state.
     *
     * @ORM\Column(name="template_id", type="integer")
     */
    private $templateId;

    /**
     * @var string Name of the state.
     *
     * @ORM\Column(name="state_name", type="string", length=50)
     */
    private $name;

    /**
     * @var string Abbreviation of the state (used in list of issues as a short-cut).
     *
     * @ORM\Column(name="state_abbr", type="string", length=50)
     */
    private $abbreviation;

    /**
     * @var int Type of the state.
     *
     * @ORM\Column(name="state_type", type="integer")
     */
    private $type;

    /**
     * @var int Type of responsibility management.
     *
     * @ORM\Column(name="responsible", type="integer")
     */
    private $responsible;

    /**
     * @var int ID of the state which is next by default.
     *
     * @ORM\Column(name="next_state_id", type="integer", nullable=true)
     */
    private $nextStateId;

    /**
     * @var Template Template of the state.
     *
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="states")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="template_id", onDelete="CASCADE")
     */
    private $template;

    /**
     * @var State Next state by default.
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="next_state_id", referencedColumnName="state_id", onDelete="CASCADE")
     */
    private $next_state;

    /**
     * @var ArrayCollection List of state fields.
     *
     * @ORM\OneToMany(targetEntity="Field", mappedBy="state")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $fields;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
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
     * @param   string $abbreviation
     *
     * @return  self
     */
    public function setAbbreviation($abbreviation)
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
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
     * @param   int $responsible
     *
     * @return  self
     */
    public function setResponsible($responsible)
    {
        $this->responsible = $responsible;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getResponsible()
    {
        return $this->responsible;
    }

    /**
     * Standard setter.
     *
     * @param   int $nextStateId
     *
     * @return  self
     */
    public function setNextStateId($nextStateId)
    {
        $this->nextStateId = $nextStateId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getNextStateId()
    {
        return $this->nextStateId;
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
    public function setNextState(State $state)
    {
        $this->next_state = $state;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  State
     */
    public function getNextState()
    {
        return $this->next_state;
    }

    /**
     * Add field to the state.
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
     * Remove field from the state.
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
     * Get list of state fields.
     *
     * @return  ArrayCollection|Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }
}
