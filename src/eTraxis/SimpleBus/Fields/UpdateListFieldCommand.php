<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Updates specified "list" field.
 *
 * @property    int $default Default value of the field.
 */
class UpdateListFieldCommand extends UpdateFieldBaseCommand
{
    /**
     * @Assert\Type(type = "int")
     * @Assert\GreaterThan(value = "0")
     */
    public $default;
}
