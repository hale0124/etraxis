<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\States;

use SimpleBus\MessageTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Removes specified transitions from specified state.
 *
 * @property    int   $id          State ID.
 * @property    int   $group       Group ID or system role.
 * @property    int[] $transitions Transitions (state IDs).
 */
class RemoveStateTransitionsCommand
{
    use MessageTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\EntityId()
     */
    public $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Any({
     *     @Assert\EntityId(),
     *     @Assert\Choice(callback = {"eTraxis\Dictionary\SystemRole", "keys"})
     * })
     */
    public $group;

    /**
     * @Assert\NotBlank()
     * @Assert\Type(type = "array")
     * @Assert\Count(min = "1", max = "100")
     * @Assert\All({
     *     @Assert\NotBlank(),
     *     @Assert\EntityId()
     * })
     */
    public $transitions;
}
