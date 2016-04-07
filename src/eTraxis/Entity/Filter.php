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
 * Filter.
 *
 * @ORM\Table(name="tbl_filters",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_filters", columns={"account_id", "filter_name"})
 *            })
 * @ORM\Entity
 */
class Filter
{
    // Constraints.
    const MAX_NAME = 50;

    // Filter type.
    const TYPE_ALL_PROJECTS  = 1;
    const TYPE_ALL_TEMPLATES = 2;
    const TYPE_ALL_STATES    = 3;
    const TYPE_SEL_STATES    = 4;

    // Filter flag.
    const FLAG_CREATED_BY  = 0x0001;
    const FLAG_ASSIGNED_TO = 0x0002;
    const FLAG_UNCLOSED    = 0x0004;
    const FLAG_POSTPONED   = 0x0008;
    const FLAG_ACTIVE      = 0x0010;
    const FLAG_UNASSIGNED  = 0x0020;

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="filter_id", type="integer")
     */
    private $id;

    /**
     * @var User Owner of the filter.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="account_id", nullable=false, referencedColumnName="account_id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string Name of the filter.
     *
     * @ORM\Column(name="filter_name", type="string", length=50)
     */
    private $name;

    /**
     * @var int Filter scope (type).
     *
     *          FILTER_TYPE_ALL_PROJECTS  - all projects
     *          FILTER_TYPE_ALL_TEMPLATES - all templates of specified project
     *          FILTER_TYPE_ALL_STATES    - all states of specified template
     *          FILTER_TYPE_SEL_STATES    - selected states of specified template
     *
     * @ORM\Column(name="filter_type", type="integer")
     */
    private $type;

    /**
     * @var int Filter flags.
     *
     *          FILTER_FLAG_CREATED_BY  - filter shows only records created by specified users
     *          FILTER_FLAG_ASSIGNED_TO - filter shows only records assigned on specified user
     *          FILTER_FLAG_UNCLOSED    - filter shows only opened records
     *          FILTER_FLAG_POSTPONED   - filter shows only postponed records
     *          FILTER_FLAG_ACTIVE      - filter shows only active (not postponed) records
     *          FILTER_FLAG_UNASSIGNED  - filter shows only unassigned records
     *
     * @ORM\Column(name="filter_flags", type="integer")
     */
    private $flags;

    /**
     * @var int Parameter of the filter. Depends on filter type as following:
     *
     *          FILTER_TYPE_ALL_PROJECTS  - NULL (not used)
     *          FILTER_TYPE_ALL_TEMPLATES - ID of the project
     *          FILTER_TYPE_ALL_STATES    - ID of the template
     *          FILTER_TYPE_SEL_STATES    - ID of the template
     *
     * @ORM\Column(name="filter_param", type="integer", nullable=true)
     */
    private $parameter;
}
