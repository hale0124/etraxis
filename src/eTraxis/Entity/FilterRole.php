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
 * Additional data for filtering by system roles.
 *
 * @ORM\Table(name="tbl_filter_accounts")
 * @ORM\Entity
 */
class FilterRole
{
    // Filter role flag.
    const AUTHOR      = 1;
    const RESPONSIBLE = 2;

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
     * @var int One of following flags:
     *
     *          FLAG_AUTHOR      - filter shows only records, created by specified users
     *          FLAG_RESPONSIBLE - filter shows only records, assigned on specified users
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="filter_flag", type="integer")
     */
    private $flag;

    /**
     * @var User User who created a record, or is its current responsible (depending on "filter_flag").
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="account_id", onDelete="CASCADE")
     */
    private $user;
}
