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
 * Returns all groups the specified account doesn't belong to.
 *
 * @property    int $id User ID.
 */
class GetOtherGroupsCommand
{
    use CommandBusTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value = "0")
     */
    public $id;
}
