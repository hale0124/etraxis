<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\DataFixtures;

use AltrEgo\AltrEgo;

/**
 * A wrapper trait for AltrEgo class.
 */
trait AltrEgoTrait
{
    /**
     * Creates and returns AltrEgo object based on specified one.
     *
     * @param   mixed $object
     *
     * @return  \StdClass
     */
    protected function ego($object)
    {
        return AltrEgo::create($object);
    }
}
