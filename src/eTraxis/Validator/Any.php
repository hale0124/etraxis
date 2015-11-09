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

use Symfony\Component\Validator\Constraints\Composite;

/**
 * A constraint to check that specified collection of constraints has at least one valid.
 *
 * @Annotation
 */
class Any extends Composite
{
    public $constraints = [];

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'constraints';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['constraints'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getCompositeOption()
    {
        return 'constraints';
    }
}
