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
 * View filter.
 *
 * @ORM\Table(name="tbl_view_filters")
 * @ORM\Entity
 */
class ViewFilter
{
    /**
     * @var View View.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="View")
     * @ORM\JoinColumn(name="view_id", referencedColumnName="view_id", onDelete="CASCADE")
     */
    private $view;

    /**
     * @var Filter Filter which is included in the view.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Filter")
     * @ORM\JoinColumn(name="filter_id", referencedColumnName="filter_id", onDelete="CASCADE")
     */
    private $filter;
}
