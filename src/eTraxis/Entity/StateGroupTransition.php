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
 * @ORM\Table(name="tbl_group_trans")
 * @ORM\Entity
 */
class StateGroupTransition
{
    /**
     * @var int State ID where issue can be moved from.
     *
     * @ORM\Column(name="state_id_from", type="integer")
     * @ORM\Id
     */
    private $fromStateId;

    /**
     * @var int State ID where issue can be moved to.
     *
     * @ORM\Column(name="state_id_to", type="integer")
     * @ORM\Id
     */
    private $toStateId;

    /**
     * @var int Group ID which is allowed to make this transition.
     *
     * @ORM\Column(name="group_id", type="integer")
     * @ORM\Id
     */
    private $groupId;

    /**
     * @var State State where issue can be moved from.
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state_id_from", referencedColumnName="state_id", onDelete="CASCADE")
     */
    private $fromState;

    /**
     * @var State State where issue can be moved to.
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state_id_to", referencedColumnName="state_id", onDelete="CASCADE")
     */
    private $toState;

    /**
     * @var Group Group which is allowed to make this transition.
     *
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="group_id", onDelete="CASCADE")
     */
    private $group;

    /**
     * Standard setter.
     *
     * @param   int $fromStateId
     *
     * @return  self
     */
    public function setFromStateId($fromStateId)
    {
        $this->fromStateId = $fromStateId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getFromStateId()
    {
        return $this->fromStateId;
    }

    /**
     * Standard setter.
     *
     * @param   int $toStateId
     *
     * @return  self
     */
    public function setToStateId($toStateId)
    {
        $this->toStateId = $toStateId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getToStateId()
    {
        return $this->toStateId;
    }

    /**
     * Standard setter.
     *
     * @param   int $groupId
     *
     * @return  self
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Standard setter.
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
     * Standard getter.
     *
     * @return  State
     */
    public function getFromState()
    {
        return $this->fromState;
    }

    /**
     * Standard setter.
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
     * Standard getter.
     *
     * @return  State
     */
    public function getToState()
    {
        return $this->toState;
    }

    /**
     * Standard setter.
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
     * Standard getter.
     *
     * @return  Group
     */
    public function getGroup()
    {
        return $this->group;
    }
}
