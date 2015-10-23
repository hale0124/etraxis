<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Users;

use eTraxis\Traits\ObjectInitiationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Enumerates all accounts existing in eTraxis database.
 *
 * Returns hash map as following:
 *   users    (array) - list of "User" entities.
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
 *     "start": 0,                          // return 10 accounts starting from first account
 *     "length": 10,
 *     "search": "test",                    // filter accounts by those which contain "test" in any property
 *     "columns": [
 *         {
 *             "data": 3,                   // also, filter accounts by those which contain "@gmail.com" in email
 *             "search": {
 *                 "value": "@gmail.com"
 *             }
 *         },
 *         {
 *             "data": 4,                   // also, filter accounts by those which has "ROLE_USER" permissions
 *             "search": {
 *                 "value": "user"
 *             }
 *         }
 *     ],
 *     "order": [
 *         {
 *             "column": 2,                 // first, order the results by full name (ascending)
 *             "dir": "asc"
 *         },
 *         {
 *             "column": 1,                 // then, order the results by user name (descending)
 *             "dir": "desc"
 *         }
 *     ]
 * }
 */
class ListUsersCommand
{
    use ObjectInitiationTrait;

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
     *                 @Assert\LessThanOrEqual(value = "6")
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
     *                 @Assert\LessThanOrEqual(value = "6")
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
