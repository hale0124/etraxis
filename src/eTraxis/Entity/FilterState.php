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

namespace eTraxis\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Additional data for filtering by states.
 *
 * @ORM\Table(name="tbl_filter_states")
 * @ORM\Entity
 */
class FilterState
{
    /**
     * @var int Filter ID.
     *
     * @ORM\Column(name="filter_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $filterId;

    /**
     * @var int State ID.
     *
     * @ORM\Column(name="state_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $stateId;

    /**
     * @var Filter Filter.
     *
     * @ORM\ManyToOne(targetEntity="Filter")
     * @ORM\JoinColumn(name="filter_id", referencedColumnName="filter_id", onDelete="CASCADE")
     */
    private $filter;

    /**
     * @var State State.
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="state_id", onDelete="CASCADE")
     */
    private $state;

    /**
     * Standard setter.
     *
     * @param   int $filterId
     *
     * @return  self
     */
    public function setFilterId($filterId)
    {
        $this->filterId = $filterId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getFilterId()
    {
        return $this->filterId;
    }

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
     * @param   Filter $filter
     *
     * @return  self
     */
    public function setFilter(Filter $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  Filter
     */
    public function getFilter()
    {
        return $this->filter;
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
}
