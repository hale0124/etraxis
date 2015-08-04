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
 * Increases locks count for specified eTraxis account.
 *
 * @property    string $username Username to lock.
 */
class LockUserCommand
{
    use CommandBusTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "112")
     */
    public $username;
}
