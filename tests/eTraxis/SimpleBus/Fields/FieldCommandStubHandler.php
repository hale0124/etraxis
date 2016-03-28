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

use eTraxis\Entity\Field;
use eTraxis\SimpleBus\Fields\Handler\BaseFieldCommandHandler;
use eTraxis\Traits\ClassAccessTrait;

/**
 * @method  Field getEntity($command)
 */
class FieldCommandStubHandler extends BaseFieldCommandHandler
{
    use ClassAccessTrait;
}
