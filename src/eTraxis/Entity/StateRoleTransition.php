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
 * State/Role transition.
 *
 * @ORM\Table(name="tbl_role_trans")
 * @ORM\Entity
 */
class StateRoleTransition
{
    /**
     * @var State State where record can be moved from.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state_id_from", referencedColumnName="state_id", onDelete="CASCADE")
     */
    private $fromState;

    /**
     * @var State State where record can be moved to.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state_id_to", referencedColumnName="state_id", onDelete="CASCADE")
     */
    private $toState;

    /**
     * @var int System role which is allowed to make this transition.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="role", type="integer")
     */
    private $role;

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
     * Property getter.
     *
     * @return  int
     */
    public function getRole()
    {
        return $this->role;
    }
}
