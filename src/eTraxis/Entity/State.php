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
 * State.
 *
 * @ORM\Table(name="tbl_states",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_states_name", columns={"template_id", "state_name"}),
 *                @ORM\UniqueConstraint(name="ix_states_abbr", columns={"template_id", "state_abbr"})
 *            })
 * @ORM\Entity(repositoryClass="eTraxis\Repository\StatesRepository")
 * @Assert\UniqueEntity(fields={"template", "name"}, message="state.conflict.name")
 * @Assert\UniqueEntity(fields={"template", "abbreviation"}, message="state.conflict.abbreviation")
 */
class State implements \JsonSerializable
{
    // Constraints.
    const MAX_NAME         = 50;
    const MAX_ABBREVIATION = 50;

    // Actions.
    const DELETE  = 'state.delete';
    const INITIAL = 'state.initial';

    // State types.
    const TYPE_INITIAL = 1;
    const TYPE_INTERIM = 2;
    const TYPE_FINAL   = 3;

    // State responsibility management.
    const RESPONSIBLE_KEEP   = 1;
    const RESPONSIBLE_ASSIGN = 2;
    const RESPONSIBLE_REMOVE = 3;

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="state_id", type="integer")
     */
    private $id;

    /**
     * @var Template Template of the state.
     *
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="states")
     * @ORM\JoinColumn(name="template_id", nullable=false, referencedColumnName="template_id", onDelete="CASCADE")
     */
    private $template;

    /**
     * @var string Name of the state.
     *
     * @ORM\Column(name="state_name", type="string", length=50)
     */
    private $name;

    /**
     * @var string Abbreviation of the state (used in list of records as a short-cut).
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
     * @ORM\OrderBy({"indexNumber" = "ASC"})
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
     * Property getter.
     *
     * @return  Template
     */
    public function getTemplate()
    {
        return $this->template;
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
     * Property getter.
     *
     * @return  string
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
    }

    /**
     * Property setter.
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
     * Property getter.
     *
     * @return  int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Property setter.
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
     * Property getter.
     *
     * @return  int
     */
    public function getResponsible()
    {
        return $this->responsible;
    }

    /**
     * Property setter.
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
     * Property getter.
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
     * @return  Field[]
     */
    public function getFields()
    {
        return $this->fields->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'abbreviation' => $this->abbreviation,
            'type'         => $this->type,
            'responsible'  => $this->responsible,
        ];
    }
}
