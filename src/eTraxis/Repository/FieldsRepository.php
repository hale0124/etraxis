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

use Doctrine\ORM\EntityRepository;

/**
 * Fields repository.
 */
class FieldsRepository extends EntityRepository
{
    /**
     * Finds all fields available for the specified state.
     *
     * @param   int $id State ID.
     *
     * @return  array
     */
    public function getFields($id)
    {
        $query = $this->createQueryBuilder('f');

        $query
            ->select('f.id')
            ->addSelect('f.stateId')
            ->addSelect('f.name')
            ->where('f.stateId = :id')
            ->andWhere('f.removedAt = 0')
            ->setParameter('id', $id)
            ->orderBy('f.indexNumber')
        ;

        return $query->getQuery()->getResult();
    }
}
