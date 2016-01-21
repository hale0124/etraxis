<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace Symfony\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * A constraint to check that specified entity ID is valid.
 *
 * @Annotation
 */
class EntityId extends Constraint
{
    /** @var string Error message. */
    public $message = 'Invalid ID.';
}
