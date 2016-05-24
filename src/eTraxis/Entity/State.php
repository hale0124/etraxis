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
 * State.
 *
 * @ORM\Table(name="tbl_states",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_states_name", columns={"template_id", "state_name"}),
 *                @ORM\UniqueConstraint(name="ix_states_abbr", columns={"template_id", "state_abbr"})
 *            })
 * @ORM\Entity
 * @ORM\EntityListeners({"eTraxis\Entity\EntityListener"})
 * @Assert\UniqueEntity(fields={"template", "name"}, message="state.conflict.name")
 * @Assert\UniqueEntity(fields={"template", "abbreviation"}, message="state.conflict.abbreviation")
 */
class State extends Entity implements \JsonSerializable
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
     * @ORM\JoinColumn(name="next_state_id", referencedColumnName="state_id", onDelete="SET NULL")
     */
    private $nextState;

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
     * @param   string $abbreviation
     *
     * @return  self
     */
    public function setAbbreviation(string $abbreviation)
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
    public function setType(int $type)
    {
        if (Dictionary\StateType::has($type)) {
            $this->type = $type;

            // Final states cannot be assigned.
            if ($type === self::TYPE_FINAL) {
                $this->responsible = self::RESPONSIBLE_REMOVE;
                $this->nextState   = null;
            }
        }

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
    public function setResponsible(int $responsible)
    {
        // Final states cannot be assigned.
        if ($this->type === self::TYPE_FINAL) {
            return $this;
        }

        if (Dictionary\StateResponsible::has($responsible)) {
            $this->responsible = $responsible;
        }

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getResponsible()
    {
        // Final states cannot be assigned.
        if ($this->type === self::TYPE_FINAL) {
            return self::RESPONSIBLE_REMOVE;
        }

        return $this->responsible;
    }

    /**
     * Property setter.
     *
     * @param   State|null $state
     *
     * @return  self
     */
    public function setNextState(State $state = null)
    {
        // Final states cannot have a next one.
        if ($this->type === self::TYPE_FINAL) {
            return $this;
        }

        // Next state must be of the same template.
        if ($state !== null && $state->getTemplate() !== $this->template) {
            return $this;
        }

        $this->nextState = $state;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  State|null
     */
    public function getNextState()
    {
        return $this->type === self::TYPE_FINAL ? null : $this->nextState;
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
     * Returns transitions available to specified system role.
     *
     * @param   int $role
     *
     * @return  State[] List of states.
     */
    public function getRoleTransitions(int $role)
    {
        // Transitions are not applicable for final states.
        if ($this->type === self::TYPE_FINAL) {
            return [];
        }

        $query = $this->manager->createQueryBuilder();

        $query
            ->select('tr')
            ->from(StateRoleTransition::class, 'tr')
            ->where('tr.fromState = :state')
            ->andWhere('tr.role = :role')
            ->setParameter('state', $this)
            ->setParameter('role', $role)
        ;

        $results = [];

        /** @var StateRoleTransition $result */
        foreach ($query->getQuery()->getResult() as $result) {
            $results[] = $result->getToState();
        }

        return $results;
    }

    /**
     * Returns transitions available to specified group.
     *
     * @param   Group $group
     *
     * @return  State[] List of states.
     */
    public function getGroupTransitions(Group $group)
    {
        // Transitions are not applicable for final states.
        if ($this->type === self::TYPE_FINAL) {
            return [];
        }

        $query = $this->manager->createQueryBuilder();

        $query
            ->select('tr')
            ->from(StateGroupTransition::class, 'tr')
            ->where('tr.fromState = :state')
            ->andWhere('tr.group = :group')
            ->setParameter('state', $this)
            ->setParameter('group', $group)
        ;

        $results = [];

        /** @var StateGroupTransition $result */
        foreach ($query->getQuery()->getResult() as $result) {
            $results[] = $result->getToState();
        }

        return $results;
    }

    /**
     * Returns list of responsible groups.
     *
     * @return  Group[] List of groups.
     */
    public function getResponsibleGroups()
    {
        // Responsible groups are applicable for assignable states only.
        if ($this->responsible !== self::RESPONSIBLE_ASSIGN) {
            return [];
        }

        $query = $this->manager->createQueryBuilder();

        $query
            ->select('srg')
            ->addSelect('g')
            ->from(StateResponsibleGroup::class, 'srg')
            ->leftJoin('srg.group', 'g')
            ->where('srg.state = :state')
            ->orderBy('g.name')
            ->setParameter('state', $this)
        ;

        $results = [];

        /** @var StateResponsibleGroup $result */
        foreach ($query->getQuery()->getResult() as $result) {
            $results[] = $result->getGroup();
        }

        return $results;
    }

    /**
     * Returns list of groups which are not responsibles.
     *
     * @return  Group[] List of groups.
     */
    public function getNotResponsibleGroups()
    {
        // Responsible groups are applicable for assignable states only.
        if ($this->responsible !== self::RESPONSIBLE_ASSIGN) {
            return [];
        }

        $groups = $this->getResponsibleGroups();

        $query = $this->manager->createQueryBuilder();

        $query
            ->select('g')
            ->from(Group::class, 'g')
            ->orderBy('g.name')
        ;

        if (count($groups) > 0) {
            $query
                ->where($query->expr()->notIn('g.id', ':groups'))
                ->setParameter('groups', $this->getResponsibleGroups())
            ;
        }

        return $query->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id'           => $this->getId(),
            'name'         => $this->getName(),
            'abbreviation' => $this->getAbbreviation(),
            'type'         => $this->getType(),
            'responsible'  => $this->getResponsible(),
        ];
    }
}
