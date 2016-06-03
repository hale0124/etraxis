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
use eTraxis\Dictionary\SystemRole;

/**
 * State/Role transition.
 *
 * @ORM\Table(name="state_role_transitions")
 * @ORM\Entity
 */
class StateRoleTransition
{
    /**
     * @var State State where record can be moved from.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="State", inversedBy="roleTransitions")
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
     * @var string System role which is allowed to make this transition.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="role", type="string", length=20)
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
     * @param   string $role
     *
     * @return  self
     */
    public function setRole(string $role)
    {
        if (SystemRole::has($role)) {
            $this->role = $role;
        }

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getRole()
    {
        return $this->role;
    }
}
