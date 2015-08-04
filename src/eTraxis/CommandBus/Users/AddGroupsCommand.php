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
 * Adds account to specified groups.
 *
 * @property    int   $id     User ID.
 * @property    int[] $groups Group IDs.
 */
class AddGroupsCommand
{
    use CommandBusTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value = "0")
     */
    public $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Type(type = "array")
     * @Assert\Count(min = "1", max = "100")
     * @Assert\All({
     *     @Assert\Type(type = "numeric"),
     *     @Assert\GreaterThan(value = "0")
     * })
     */
    public $groups;
}
