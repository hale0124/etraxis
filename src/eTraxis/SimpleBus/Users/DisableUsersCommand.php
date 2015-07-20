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
 * Disables specified accounts.
 *
 * @property    int[] $ids User IDs.
 */
class DisableUsersCommand
{
    use InitializationTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Type(type = "array")
     * @Assert\Count(min = "1", max = "100")
     * @Assert\All({
     *     @Assert\Type(type = "numeric"),
     *     @Assert\GreaterThan(value = "0")
     * })
     */
    public $ids;
}
