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

namespace eTraxis\SimpleBus\Command\User;

use eTraxis\Traits;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Finds specified account.
 *
 * Input properties:
 * @property    int $id User ID.
 *
 * Output properties:
 * @property    \eTraxis\Model\User $user User.
 */
class FindUserCommand
{
    use Traits\InitializationTrait;
    use Traits\GetTrait;
    use Traits\SetTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value = "0")
     */
    protected $id;

    protected $user;
}
