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
use eTraxis\Entity\Group;
use eTraxis\Entity\User;

/**
 * Users repository.
 */
class UsersRepository extends EntityRepository
{
    /**
     * Finds all groups the specified account belongs to.
     *
     * @param   User $user
     *
     * @return  Group[]
     */
    public function getUserGroups(User $user)
    {
        $repository = $this->getEntityManager()->getRepository(Group::class);

        $query = $repository->createQueryBuilder('g');

        $query
            ->select('g')
            ->addSelect('p')
            ->join('g.users', 'u')
            ->leftJoin('g.project', 'p')
            ->where('u = :user')
            ->setParameter('user', $user)
            ->orderBy('p.name')
            ->addOrderBy('g.name')
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * Finds all groups the specified account doesn't belong to.
     *
     * @param   User $user
     *
     * @return  Group[]
     */
    public function getOtherGroups(User $user)
    {
        $repository = $this->getEntityManager()->getRepository(Group::class);

        // Find all groups the account belong to.
        $subquery = $repository->createQueryBuilder('g');

        $subquery
            ->select('g.id')
            ->join('g.users', 'u')
            ->where('u = :user')
            ->setParameter('user', $user)
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
