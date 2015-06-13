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

use eTraxis\SimpleBus\CommandTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Increases locks count for specified eTraxis account.
 *
 * Input properties:
 *
 * @property    string $username Username to lock.
 *
 * Output properties:
 *
 * None.
 */
class LockUserCommand
{
    use CommandTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="112")
     */
    protected $username;
}
