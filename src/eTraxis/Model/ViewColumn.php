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
 * View column.
 *
 * @ORM\Table(name="tbl_view_columns",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_view_columns_name", columns={"view_id", "state_name", "field_name", "column_type"}),
 *                @ORM\UniqueConstraint(name="ix_view_columns_order", columns={"view_id", "column_order"})
 *            })
 * @ORM\Entity
 */
class ViewColumn
{
    // System column type.
    const TYPE_ID            = 1;
    const TYPE_PROJECT       = 2;
    const TYPE_STATE_ABBR    = 3;
    const TYPE_SUBJECT       = 4;
    const TYPE_AUTHOR        = 5;
    const TYPE_RESPONSIBLE   = 6;
    const TYPE_LAST_EVENT    = 7;
    const TYPE_AGE           = 8;
    const TYPE_CREATION_DATE = 9;
    const TYPE_TEMPLATE      = 10;
    const TYPE_STATE_NAME    = 11;
    const TYPE_LAST_STATE    = 12;

    // Custom column type.
    const TYPE_NUMBER      = 100;
    const TYPE_STRING      = 101;
    const TYPE_MULTILINED  = 102;
    const TYPE_CHECKBOX    = 103;
    const TYPE_LIST_NUMBER = 104;
    const TYPE_LIST_STRING = 105;
    const TYPE_RECORD      = 106;
    const TYPE_DATE        = 107;
    const TYPE_DURATION    = 108;
    const TYPE_DECIMAL     = 109;

    /**
     * @var int Unique ID.
     *
     * @ORM\Column(name="column_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int View ID.
     *
     * @ORM\Column(name="view_id", type="integer")
     */
    private $viewId;

    /**
     * @var string Name of state which owns a field this column is linked to.
     *
     * @ORM\Column(name="state_name", type="string", length=50, nullable=true)
     */
    private $stateName;

    /**
     * @var string Name of field which this column is linked to.
     *
     * @ORM\Column(name="field_name", type="string", length=50, nullable=true)
     */
    private $fieldName;

    /**
     * @var int Column type.
     *
     * @ORM\Column(name="column_type", type="integer")
     */
    private $type;

    /**
     * @var int Ordinal number of the column (from 1 till amount of columns in the same view).
     *          Each column must have its own ordinal number.
     *          No duplicates of this number among columns of the same view are allowed.
     *
     * @ORM\Column(name="column_order", type="integer")
     */
    private $order;

    /**
     * @var View View.
     *
     * @ORM\ManyToOne(targetEntity="View")
     * @ORM\JoinColumn(name="view_id", referencedColumnName="view_id")
     */
    private $view;

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Standard setter.
     *
     * @param   int $viewId
     *
     * @return  self
     */
    public function setViewId($viewId)
    {
        $this->viewId = $viewId;

        return $this;
    }

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
     * Standard setter.
     *
     * @param   string $stateName
     *
     * @return  self
     */
    public function setStateName($stateName)
    {
        $this->stateName = $stateName;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getStateName()
    {
        return $this->stateName;
    }

    /**
     * Standard setter.
     *
     * @param   string $fieldName
     *
     * @return  self
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * Standard setter.
     *
     * @param   int $type
     *
     * @return  self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Standard setter.
     *
     * @param   int $order
     *
     * @return  self
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getOrder()
    {
        return $this->order;
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
}
