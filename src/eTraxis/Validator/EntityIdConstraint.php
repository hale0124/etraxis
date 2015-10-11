<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * A contraint to check that specified entity ID is valid.
 *
 * @Annotation
 */
class EntityIdConstraint extends Constraint
{
    /** @var string Error message. */
    public $message = 'Invalid ID.';
}
