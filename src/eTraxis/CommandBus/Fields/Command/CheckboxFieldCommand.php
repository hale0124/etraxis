<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Fields\Command;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Create/update command for "checkbox" field.
 *
 * @property    bool $defaultValue Default value of the field.
 */
class CheckboxFieldCommand extends FieldCommand
{
    /**
     * @Assert\NotNull()
     */
    public $defaultValue;
}
