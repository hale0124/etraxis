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
 * View filter.
 *
 * @ORM\Table(name="tbl_view_filters")
 * @ORM\Entity
 */
class ViewFilter
{
    /**
     * @var int View ID.
     *
     * @ORM\Column(name="view_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $viewId;

    /**
     * @var int Filter ID which is included in the view.
     *
     * @ORM\Column(name="filter_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $filterId;

    /**
     * @var View View.
     *
     * @ORM\ManyToOne(targetEntity="View")
     * @ORM\JoinColumn(name="view_id", referencedColumnName="view_id")
     */
    private $view;

    /**
     * @var Filter Filter which is included in the view.
     *
     * @ORM\ManyToOne(targetEntity="Filter")
     * @ORM\JoinColumn(name="filter_id", referencedColumnName="filter_id")
     */
    private $filter;

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getViewId()
    {
        return $this->viewId;
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
     * @param   View $view
     *
     * @return  self
     */
    public function setView(View $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  View
     */
    public function getView()
    {
        return $this->view;
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
}
