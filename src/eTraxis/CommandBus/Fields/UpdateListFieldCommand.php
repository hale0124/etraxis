<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Fields;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Updates specified "list" field.
 *
 * @property    int $defaultValue Default value of the field.
 */
class UpdateListFieldCommand extends Command\ListFieldCommand
{
    use Command\UpdateFieldCommandTrait;

    /**
     * @Assert\Regex("/^(\-|\+)?\d+$/")
     * @Assert\GreaterThan(value="0")
     */
    public $defaultValue;
}
