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
 * Disables specified accounts.
 *
 * Input properties:
 * @property    int[] $ids User IDs.
 *
 * Output properties: none.
 */
class DisableUsersCommand
{
    use Traits\InitializationTrait;
    use Traits\GetTrait;
    use Traits\SetTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Type(type = "array")
     * @Assert\Count(min = "1", max = "100")
     * @Assert\All({
     *     @Assert\Type(type = "numeric"),
     *     @Assert\GreaterThan(value = "0")
     * })
     */
    protected $ids;
}
