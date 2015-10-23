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
 * Removes account from specified groups.
 *
 * @property    int   $id     User ID.
 * @property    int[] $groups Group IDs.
 */
class RemoveGroupsCommand
{
    use ObjectInitiationTrait;

    /**
     * @Assert\NotBlank()
     * @eTraxis\Validator\EntityIdConstraint()
     */
    public $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Type(type = "array")
     * @Assert\Count(min = "1", max = "100")
     * @Assert\All({
     *     @Assert\NotBlank(),
     *     @eTraxis\Validator\EntityIdConstraint()
     * })
     */
    public $groups;
}
