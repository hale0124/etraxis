<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Repository;

use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Custom values repository.
 */
interface CustomValuesRepositoryInterface extends ObjectRepository
{
    /**
     * Saves specified custom value in the repository and returns its ID.
     *
     * @param   mixed $value Custom value.
     *
     * @return  int Value ID.
     */
    public function save($value);
}
