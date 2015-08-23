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

namespace eTraxis\CommandBus\Users;

use eTraxis\Traits\CommandBusTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resets password for specified account.
 *
 * @property    string $token    Token for password reset.
 * @property    string $password New password.
 */
class ResetPasswordCommand
{
    use CommandBusTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^[a-z0-9]{32}$/i");
     */
    public $token;

    /**
     * @Assert\NotBlank()
     */
    public $password = null;
}
