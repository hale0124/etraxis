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

use eTraxis\Traits;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Disables specified account.
 *
 * Input properties:
 * @property    int $id User ID.
 *
 * Output properties: none.
 */
class DisableUserCommand
{
    use Traits\InitializationTrait;
    use Traits\GetTrait;
    use Traits\SetTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value = "0")
     */
    protected $id;
}
