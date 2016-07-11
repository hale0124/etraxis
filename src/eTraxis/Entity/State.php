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
 * @ORM\Table(name="states",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(columns={"template_id", "name"}),
 *                @ORM\UniqueConstraint(columns={"template_id", "abbreviation"})
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
    const MAX_ABBREVIATION = 3;

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var Template Template of the state.
     *
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="states")
     * @ORM\JoinColumn(name="template_id", nullable=false, referencedColumnName="id", onDelete="CASCADE")
     */
    private $template;

    /**
     * @var string Name of the state.
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string Abbreviation of the state (used in list of records as a short-cut).
     *
     * @ORM\Column(name="abbreviation", type="string", length=3)
     */
    private $abbreviation;

    /**
     * @var string Type of the state.
     *
     * @ORM\Column(name="type", type="string", length=10)
     */
    private $type;

    /**
     * @var string Type of responsibility management.
     *
     * @ORM\Column(name="responsible", type="string", length=10)
     */
    private $responsible;

    /**
     * @var State Next state by default.
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="next_state_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $nextState;

    /**
     * @var ArrayCollection List of state fields.
     *
     * @ORM\OneToMany(targetEntity="Field", mappedBy="state")
     * @ORM\OrderBy({"order" = "ASC"})
     */
    private $fields;

    /**
     * @var ArrayCollection List of role transitions.
     *
     * @ORM\OneToMany(targetEntity="StateRoleTransition", mappedBy="fromState", cascade={"persist"})
     */
    private $roleTransitions;

    /**
     * @var ArrayCollection List of group transitions.
     *
     * @ORM\OneToMany(targetEntity="StateGroupTransition", mappedBy="fromState", cascade={"persist"})
     */
    private $groupTransitions;

    /**
     * @var ArrayCollection List of responsible group.
     *
     * @ORM\OneToMany(targetEntity="StateResponsibleGroup", mappedBy="state", cascade={"persist"})
     */
    private $responsibleGroups;

    /**
     * Creates new state in the specified template.
     *
     * @param   Template $template
     * @param   string   $type
     */
    public function __construct(Template $template, string $type)
    {
        $this->template = $template;

        if (Dictionary\StateType::has($type)) {
            $this->type = $type;
        }

        // Final states cannot be assigned.
        if ($type === Dictionary\StateType::IS_FINAL) {
            $this->responsible = Dictionary\StateResponsible::REMOVE;
        }

        $this->fields            = new ArrayCollection();
        $this->roleTransitions   = new ArrayCollection();
        $this->groupTransitions  = new ArrayCollection();
        $this->responsibleGroups = new ArrayCollection();
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
     * @param   string $responsible
     *
     * @return  self
     */
    public function setResponsible(string $responsible)
    {
        // Final states cannot be assigned.
        if ($this->type === Dictionary\StateType::IS_FINAL) {
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
     * @return  string
     */
    public function getResponsible()
    {
        // Final states cannot be assigned.
        if ($this->type === Dictionary\StateType::IS_FINAL) {
            return Dictionary\StateResponsible::REMOVE;
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
        if ($this->type === Dictionary\StateType::IS_FINAL) {
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
        return $this->type === Dictionary\StateType::IS_FINAL ? null : $this->nextState;
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
     * Sets transitions of specified role.
     *
     * @param   string  $role
     * @param   State[] $states
     *
     * @return  self
     */
    public function setRoleTransitions(string $role, array $states)
    {
        /** @var State[] $toAdd */
        $toAdd = array_unique(array_diff($states, $this->getRoleTransitions($role)));

        // Remove extra transitions.
        foreach ($this->roleTransitions as $key => $transition) {
            /** @var StateRoleTransition $transition */
            if ($transition->getRole() === $role) {
                if (!in_array($transition->getToState(), $states)) {
                    $this->roleTransitions->remove($key);
                    $this->manager->remove($transition);
                }
            }
        }

        // Grant required transitions.
        foreach ($toAdd as $state) {
            $transition = new StateRoleTransition($this, $state, $role);
            $this->roleTransitions->add($transition);
        }

        return $this;
    }

    /**
     * Returns transitions of specified role.
     *
     * @param   string $role
     *
     * @return  State[]
     */
    public function getRoleTransitions(string $role)
    {
        // Transitions are not applicable for final states.
        if ($this->type === Dictionary\StateType::IS_FINAL) {
            return [];
        }

        // Filter all transitions by the role.
        $transitions = $this->roleTransitions->filter(function (StateRoleTransition $transition) use ($role) {
            return $transition->getRole() === $role;
        });

        // Retrieve the destination state.
        $filtered = $transitions->map(function (StateRoleTransition $transition) {
            return $transition->getToState();
        });

        return array_values($filtered->toArray());
    }

    /**
     * Sets transitions of specified group.
     *
     * @param   Group   $group
     * @param   State[] $states
     *
     * @return  self
     */
    public function setGroupTransitions(Group $group, array $states)
    {
        /** @var State[] $toAdd */
        $toAdd = array_unique(array_diff($states, $this->getGroupTransitions($group)));

        // Remove extra transitions.
        foreach ($this->groupTransitions as $key => $transition) {
            /** @var StateGroupTransition $transition */
            if ($transition->getGroup() === $group) {
                if (!in_array($transition->getToState(), $states)) {
                    $this->groupTransitions->remove($key);
                    $this->manager->remove($transition);
                }
            }
        }

        // Grant required transitions.
        foreach ($toAdd as $state) {
            $transition = new StateGroupTransition($this, $state, $group);
            $this->groupTransitions->add($transition);
        }

        return $this;
    }

    /**
     * Returns transitions of specified group.
     *
     * @param   Group $group
     *
     * @return  State[]
     */
    public function getGroupTransitions(Group $group)
    {
        // Transitions are not applicable for final states.
        if ($this->type === Dictionary\StateType::IS_FINAL) {
            return [];
        }

        // Filter all transitions by the group.
        $transitions = $this->groupTransitions->filter(function (StateGroupTransition $transition) use ($group) {
            return $transition->getGroup() === $group;
        });

        // Retrieve the destination state.
        $filtered = $transitions->map(function (StateGroupTransition $transition) {
            return $transition->getToState();
        });

        return array_values($filtered->toArray());
    }

    /**
     * Adds specified responsible groups.
     *
     * @param   Group[] $groups
     *
     * @return  self
     */
    public function addResponsibleGroups(array $groups)
    {
        $current = $this->responsibleGroups->map(function (StateResponsibleGroup $responsibleGroup) {
            return $responsibleGroup->getGroup();
        });

        $toAdd = array_diff($groups, $current->toArray());

        foreach ($toAdd as $group) {
            $responsibleGroup = new StateResponsibleGroup($this, $group);
            $this->responsibleGroups->add($responsibleGroup);
        }

        return $this;
    }

    /**
     * Removes specified responsible groups.
     *
     * @param   Group[] $groups
     *
     * @return  self
     */
    public function removeResponsibleGroups(array $groups)
    {
        foreach ($this->responsibleGroups as $key => $group) {
            /** @var StateResponsibleGroup $group */
            if (in_array($group->getGroup(), $groups)) {
                $this->responsibleGroups->remove($key);
                $this->manager->remove($group);
            }
        }

        return $this;
    }

    /**
     * Returns list of responsible groups.
     *
     * @return  Group[] List of groups.
     */
    public function getResponsibleGroups()
    {
        // Responsible groups are applicable for assignable states only.
        if ($this->responsible !== Dictionary\StateResponsible::ASSIGN) {
            return [];
        }

        return array_map(function (StateResponsibleGroup $group) {
            return $group->getGroup();
        }, $this->responsibleGroups->toArray());
    }

    /**
     * Returns list of groups which are not responsibles.
     *
     * @return  Group[] List of groups.
     */
    public function getNotResponsibleGroups()
    {
        // Responsible groups are applicable for assignable states only.
        if ($this->responsible !== Dictionary\StateResponsible::ASSIGN) {
            return [];
        }

        $query = $this->manager->createQueryBuilder();

        $query
            ->select('g')
            ->from(Group::class, 'g')
            ->orderBy('g.name')
        ;

        if (count($this->responsibleGroups) > 0) {
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
    public function __toString()
    {
        return 'state#' . $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id'           => $this->getId(),
            'template'     => $this->getTemplate()->getId(),
            'name'         => $this->getName(),
            'abbreviation' => $this->getAbbreviation(),
            'type'         => $this->getType(),
            'responsible'  => $this->getResponsible(),
        ];
    }
}
