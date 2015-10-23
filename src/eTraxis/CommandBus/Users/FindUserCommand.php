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
 * Finds specified account.
 *
 * Returns found user object.
 *
 * @property    int $id User ID.
 */
class FindUserCommand
{
    use ObjectInitiationTrait;

    /**
     * @Assert\NotBlank()
     * @eTraxis\Validator\EntityIdConstraint()
     */
    public $id;
}
