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
 * State possible assignees.
 *
 * @ORM\Table(name="tbl_state_assignees")
 * @ORM\Entity
 */
class StateAssignee
{
    /**
     * @var int State ID.
     *
     * @ORM\Column(name="state_id", type="integer")
     * @ORM\Id
     */
    private $stateId;

    /**
     * @var int Group ID.
     *
     * @ORM\Column(name="group_id", type="integer")
     * @ORM\Id
     */
    private $groupId;

    /**
     * @var State State where record can be moved to.
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="state_id", onDelete="CASCADE")
     */
    private $state;

    /**
     * @var Group Group which members can be assigned on this transition.
     *
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="group_id", onDelete="CASCADE")
     */
    private $group;

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
     * Standard getter.
     *
     * @return  State
     */
    public function getState()
    {
        return $this->state;
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
