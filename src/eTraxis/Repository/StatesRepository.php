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
 * States repository.
 */
class StatesRepository extends EntityRepository
{
    /**
     * Finds all states available for the specified template.
     *
     * @param   int $id Template ID.
     *
     * @return  array
     */
    public function getStates($id)
    {
        $query = $this->createQueryBuilder('s');

        $query
            ->select('s.id')
            ->addSelect('s.templateId')
            ->addSelect('s.name')
            ->addSelect('s.abbreviation')
            ->addSelect('s.type')
            ->addSelect('s.responsible')
            ->where('s.templateId = :id')
            ->setParameter('id', $id)
            ->orderBy('s.type')
            ->addOrderBy('s.name')
        ;

        return $query->getQuery()->getResult();
    }
}
