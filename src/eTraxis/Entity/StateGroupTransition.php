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

/**
 * State/Group transition.
 *
 * @ORM\Table(name="state_group_transitions")
 * @ORM\Entity
 */
class StateGroupTransition
{
    /**
     * @var State State where record can be moved from.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="State", inversedBy="groupTransitions")
     * @ORM\JoinColumn(name="state_id_from", referencedColumnName="id", onDelete="CASCADE")
     */
    private $fromState;

    /**
     * @var State State where record can be moved to.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state_id_to", referencedColumnName="id", onDelete="CASCADE")
     */
    private $toState;

    /**
     * @var Group Group which is allowed to make this transition.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $group;

    /**
     * Property setter.
     *
     * @param   State $fromState
     *
     * @return  self
     */
    public function setFromState(State $fromState)
    {
        $this->fromState = $fromState;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  State
     */
    public function getFromState()
    {
        return $this->fromState;
    }

    /**
     * Property setter.
     *
     * @param   State $toState
     *
     * @return  self
     */
    public function setToState(State $toState)
    {
        $this->toState = $toState;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  State
     */
    public function getToState()
    {
        return $this->toState;
    }

    /**
     * Property setter.
     *
     * @param   Group $group
     *
     * @return  self
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  Group
     */
    public function getGroup()
    {
        return $this->group;
    }
}
