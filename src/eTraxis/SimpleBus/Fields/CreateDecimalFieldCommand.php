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

/**
 * Creates new "decimal" field.
 */
class CreateDecimalFieldCommand extends Command\DecimalFieldCommand
{
    use Command\CreateFieldCommandTrait;
}
