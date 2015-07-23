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

use eTraxis\SimpleBus\BaseCommand;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Deletes specified account.
 *
 * @property    int $id User ID.
 */
class DeleteUserCommand extends BaseCommand
{
    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value = "0")
     */
    public $id;
}
