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
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="column_id", type="integer")
     */
    private $id;

    /**
     * @var View View.
     *
     * @ORM\ManyToOne(targetEntity="View")
     * @ORM\JoinColumn(name="view_id", nullable=false, referencedColumnName="view_id", onDelete="CASCADE")
     */
    private $view;

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
}
