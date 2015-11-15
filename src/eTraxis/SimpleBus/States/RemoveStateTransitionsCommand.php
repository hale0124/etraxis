<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\States;

use eTraxis\Traits\ObjectInitiationTrait;
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
    use ObjectInitiationTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\EntityId()
     */
    public $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Any({
     *     @Assert\EntityId(),
     *     @Assert\Choice(callback = {"eTraxis\Collection\SystemRole", "getAllKeys"})
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
