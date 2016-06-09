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
 * State responsible groups.
 *
 * @ORM\Table(name="state_responsibles")
 * @ORM\Entity
 */
class StateResponsibleGroup
{
    /**
     * @var State State where record can be moved to.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="State", inversedBy="responsibleGroups")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $state;

    /**
     * @var Group Group which members can be assigned during the transition.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $group;

    /**
     * Constructor.
     *
     * @param   State $state
     * @param   Group $group
     */
    public function __construct(State $state, Group $group)
    {
        $this->state = $state;
        $this->group = $group;
    }

    /**
     * Property getter.
     *
     * @return  State
     */
    public function getState()
    {
        return $this->state;
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
