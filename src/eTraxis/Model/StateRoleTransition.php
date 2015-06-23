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

namespace eTraxis\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * State/Role transition.
 *
 * @ORM\Table(name="tbl_role_trans")
 * @ORM\Entity
 */
class StateRoleTransition
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
     * @var int System role which is allowed to make this transition.
     *
     * @ORM\Column(name="role", type="integer")
     * @ORM\Id
     */
    private $role;

    /**
     * @var State State where issue can be moved from.
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state_id_from", referencedColumnName="state_id")
     */
    private $fromState;

    /**
     * @var State State where issue can be moved to.
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state_id_to", referencedColumnName="state_id")
     */
    private $toState;

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
     * @param   int $role
     *
     * @return  self
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getRole()
    {
        return $this->role;
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
}
