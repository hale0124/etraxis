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
 * Increases locks count for specified eTraxis account.
 *
 * @property    string $username Username to lock.
 */
class LockUserCommand extends BaseCommand
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "112")
     */
    public $username;
}
