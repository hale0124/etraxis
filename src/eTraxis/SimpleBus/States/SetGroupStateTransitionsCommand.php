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

use eTraxis\Traits\MessageTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sets specified transitions from specified state.
 *
 * @property    int   $id          State ID.
 * @property    int   $group       Group ID.
 * @property    int[] $transitions Transitions (state IDs).
 */
class SetGroupStateTransitionsCommand
{
    use MessageTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    public $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    public $group;

    /**
     * @Assert\Type(type="array")
     * @Assert\Count(min="0", max="100")
     * @Assert\All({
     *     @Assert\NotBlank(),
     *     @Assert\Regex("/^\d+$/")
     * })
     */
    public $transitions;
}
