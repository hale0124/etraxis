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
 * Deletes specified account.
 *
 * @property    int $id User ID.
 */
class DeleteUserCommand
{
    use CommandBusTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value = "0")
     */
    public $id;
}
