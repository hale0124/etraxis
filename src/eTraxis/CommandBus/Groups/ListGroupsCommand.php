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

namespace eTraxis\CommandBus\Groups;

use eTraxis\Traits\CommandBusTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Enumerates all groups existing in eTraxis database.
 *
 * Returns hash map as following:
 *   groups   (array) - list of "Group" entities.
 *   filtered (int)   - total number of rows in the list.
 *   total    (int)   - total number of rows in the database.
 *
 * @property    int    $start   First row to return, zero-based.
 * @property    int    $length  Total number of rows to return (-1 to return all rows).
 * @property    string $search  Current global search value.
 * @property    array  $columns Columns information (searchable, orderable, current search value, etc).
 * @property    array  $order   Current columns ordering (zero-based column index and direction).
 *
 * Sample of valid request (JSON notation):
 *
 * {
 *     "start": 0,                          // return 10 groups starting from first one
 *     "length": 10,
 *     "search": "test",                    // filter groups by those which contain "test" in any property
 *     "columns": [
 *         {
 *             "data": 1,                   // also, filter groups by those which contain "clients" in group name
 *             "search": {
 *                 "value": "clients"
 *             }
 *         },
 *         {
 *             "data": 2,                   // also, filter groups by those which are local
 *             "search": {
 *                 "value": "local"
 *             }
 *         }
 *     ],
 *     "order": [
 *         {
 *             "column": 3,                 // first, order the results by project name (ascending)
 *             "dir": "asc"
 *         },
 *         {
 *             "column": 1,                 // then, order the results by group name (descending)
 *             "dir": "desc"
 *         }
 *     ]
 * }
 */
class ListGroupsCommand
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
     *             "data" = {
     *                 @Assert\GreaterThanOrEqual(value = "0"),
     *                 @Assert\LessThanOrEqual(value = "4")
     *             },
     *             "search" = {
     *                 @Assert\Collection(
     *                     fields = {
     *                         "value" = {
     *                             @Assert\Length(max = "100")
     *                         }
     *                     },
     *                     allowExtraFields   = true,
     *                     allowMissingFields = false
     *                 )
     *             }
     *         },
     *         allowExtraFields   = true,
     *         allowMissingFields = false
     *     )
     * })
     */
    public $columns = [];

    /**
     * @Assert\NotNull()
     * @Assert\Type(type = "array")
     * @Assert\All({
     *     @Assert\Collection(
     *         fields = {
     *             "column" = {
     *                 @Assert\GreaterThanOrEqual(value = "0"),
     *                 @Assert\LessThanOrEqual(value = "4")
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
