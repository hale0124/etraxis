<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Groups;

use eTraxis\Traits\ObjectInitiationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Adds specified accounts to group.
 *
 * @property    int   $id    Group ID.
 * @property    int[] $users User IDs.
 */
class AddUsersCommand
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
    public $users;
}
