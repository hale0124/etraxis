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
     * Finds all groups the specified account doesn't belong to.
     *
     * @param   User $user
     *
     * @return  Group[]
     */
    public function getOtherGroups(User $user)
    {
        $repository = $this->getEntityManager()->getRepository(Group::class);

        $query = $repository->createQueryBuilder('g');

        $query
            ->select('g')
            ->addSelect('p')
            ->leftJoin('g.project', 'p')
            ->orderBy('p.name')
            ->addOrderBy('g.name')
        ;

        $groups = $user->getGroups();

        if (count($groups) > 0) {
            $query
                ->where($query->expr()->notIn('g', ':groups'))
                ->setParameter('groups', $groups)
            ;
        }

        return $query->getQuery()->getResult();
    }
}
