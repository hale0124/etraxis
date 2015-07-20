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
 * Clears locks count for specified eTraxis account.
 *
 * @property    string $username Username to unlock.
 */
class UnlockUserCommand
{
    use InitializationTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "112")
     */
    public $username;
}
