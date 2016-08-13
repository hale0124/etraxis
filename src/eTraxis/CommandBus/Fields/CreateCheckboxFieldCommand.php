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
 * Creates new "checkbox" field.
 */
class CreateCheckboxFieldCommand extends Command\CheckboxFieldCommand
{
    use Command\CreateFieldCommandTrait;
}
