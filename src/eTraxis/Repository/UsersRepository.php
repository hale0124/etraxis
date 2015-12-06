<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Users repository.
 */
class UsersRepository extends EntityRepository
{
    /**
     * Finds all groups the specified account belongs to.
     *
     * @param   int $id User ID.
     *
     * @return  \eTraxis\Entity\Group[]
     */
    public function getUserGroups($id)
    {
        $repository = $this->getEntityManager()->getRepository('eTraxis:Group');

        $query = $repository->createQueryBuilder('g');

        $query
            ->select('g')
            ->addSelect('p')
            ->join('g.users', 'u')
            ->leftJoin('g.project', 'p')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->orderBy('p.name')
            ->addOrderBy('g.name')
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * Finds all groups the specified account doesn't belong to.
     *
     * @param   int $id User ID.
     *
     * @return  \eTraxis\Entity\Group[]
     */
    public function getOtherGroups($id)
    {
        if (!$this->find($id)) {
            return [];
        }

        $repository = $this->getEntityManager()->getRepository('eTraxis:Group');

        // Find all groups the account belong to.
        $subquery = $repository->createQueryBuilder('g');

        $subquery
            ->select('g.id')
            ->join('g.users', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
        ;

        $groups = $subquery->getQuery()->getArrayResult();

        // Find all other groups.
        $query = $repository->createQueryBuilder('g');

        $query
            ->select('g')
            ->addSelect('p')
            ->leftJoin('g.project', 'p')
            ->orderBy('p.name')
            ->addOrderBy('g.name')
        ;

        if (count($groups)) {

            $query
                ->where($query->expr()->notIn('g.id', ':groups'))
                ->setParameter('groups', $groups)
            ;
        }

        return $query->getQuery()->getResult();
    }
}
