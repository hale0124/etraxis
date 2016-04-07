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
 * Additional data for filtering by states transitions.
 *
 * @ORM\Table(name="tbl_filter_trans")
 * @ORM\Entity
 */
class FilterTransition
{
    /**
     * @var Filter Filter.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Filter")
     * @ORM\JoinColumn(name="filter_id", referencedColumnName="filter_id", onDelete="CASCADE")
     */
    private $filter;

    /**
     * @var State State that a record was moved to.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="state_id", onDelete="CASCADE")
     */
    private $state;

    /**
     * @var int Minimum date value of the timestamps range (always means 0:00 time of specified day).
     *
     * @ORM\Column(name="date1", type="integer")
     */
    private $date1;

    /**
     * @var int Maximum date value of the timestamps range (always means 0:00 time of specified day).
     *
     * @ORM\Column(name="date2", type="integer")
     */
    private $date2;
}
