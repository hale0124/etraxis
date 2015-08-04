<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Users;

use eTraxis\Traits\CommandBusTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Enumerates all accounts existing in eTraxis database.
 *
 * Returns hash map as following:
 *   users    (array) - list of "User" entities.
 *   filtered (int)   - total number of rows in the list.
 *   total    (int)   - total number of rows in the database.
 *
 * @property    int    $start  First row to return, zero-based.
 * @property    int    $length Total number of rows to return (-1 to return all rows).
 * @property    string $search Current search value.
 * @property    array  $order  Current columns ordering (zero-based column index and direction).
 */
class ListUsersCommand
{
    use CommandBusTrait;

    /**
     * @Assert\NotNull()
     * @Assert\GreaterThanOrEqual(value = "0")
     */
    public $start = 0;

    /**
     * @Assert\NotNull()
     * @Assert\GreaterThanOrEqual(value = "-1")
     */
    public $length = -1;

    /**
     * @Assert\Length(max = "100")
     */
    public $search = null;

    /**
     * @Assert\NotNull()
     * @Assert\Type(type = "array")
     * @Assert\All({
     *     @Assert\Collection(
     *         fields = {
     *             "column" = {
     *                 @Assert\GreaterThanOrEqual(value = "0"),
     *                 @Assert\LessThan(value = "6")
     *             },
     *             "dir" = {
     *                 @Assert\Choice(choices = {"asc", "desc"})
     *             }
     *         },
     *         allowExtraFields   = false,
     *         allowMissingFields = false
     *     )
     * })
     */
    public $order = [];
}
