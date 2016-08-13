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

/**
 * Updates specified "number" field.
 */
class UpdateNumberFieldCommand extends Command\NumberFieldCommand
{
    use Command\UpdateFieldCommandTrait;
}
