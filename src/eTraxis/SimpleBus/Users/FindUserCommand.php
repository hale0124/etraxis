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

namespace eTraxis\SimpleBus\Users;

use eTraxis\Traits\InitializationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Finds specified account.
 *
 * @property    int $id User ID.
 */
class FindUserCommand
{
    use InitializationTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value = "0")
     */
    public $id;

    /** @var \eTraxis\Entity\User Found user. */
    public $user = null;
}
