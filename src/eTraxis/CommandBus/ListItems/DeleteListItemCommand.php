<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\ListItems;

use eTraxis\Traits\CommandTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Deletes specified list item.
 *
 * @property    int $field ID of the item's field.
 * @property    int $value Item value.
 */
class DeleteListItemCommand
{
    use CommandTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    public $field;

    /**
     * @Assert\NotBlank()
     * @Assert\Range(min="1")
     */
    public $value;
}
