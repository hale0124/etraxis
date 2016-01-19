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
 * Groups repository.
 */
class GroupsRepository extends EntityRepository
{
    /**
     * Finds all groups available for the specified project.
     *
     * @param   int $id Project ID.
     *
     * @return  array
     */
    public function getGroups($id)
    {
        $repository = $this->getEntityManager()->getRepository('eTraxis:Group');

        $query = $repository->createQueryBuilder('g');

        $query
            ->select('g.id')
            ->addSelect('g.name')
            ->addSelect('g.projectId')
            ->where('g.projectId IS NULL')
            ->orWhere('g.projectId = :id')
            ->setParameter('id', $id)
            ->orderBy('g.name')
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * Finds all accounts which belong to the specified group.
     *
     * @param   int $id Group ID.
     *
     * @return  \eTraxis\Entity\User[]
     */
    public function getGroupMembers($id)
    {
        $repository = $this->getEntityManager()->getRepository('eTraxis:User');

        $query = $repository->createQueryBuilder('u');

        $query
            ->select('u')
            ->join('u.groups', 'g')
            ->where('g.id = :id')
            ->setParameter('id', $id)
            ->orderBy('u.fullname')
            ->addOrderBy('u.username')
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * Finds all accounts which doesn't belong to the specified group.
     *
     * @param   int $id Group ID.
     *
     * @return  \eTraxis\Entity\User[]
     */
    public function getGroupNonMembers($id)
    {
        if (!$this->find($id)) {
            return [];
        }

        $repository = $this->getEntityManager()->getRepository('eTraxis:User');

        // Find all accounts which belong to the group.
        $subquery = $repository->createQueryBuilder('u');

        $subquery
            ->select('u.id')
            ->join('u.groups', 'g')
            ->where('g.id = :id')
            ->setParameter('id', $id)
        ;

        $members = $subquery->getQuery()->getArrayResult();

        // Find all other accounts.
        $query = $repository->createQueryBuilder('u');

        $query
            ->select('u')
            ->orderBy('u.fullname')
            ->addOrderBy('u.username')
        ;

        if (count($members)) {

            $query
                ->where($query->expr()->notIn('u.id', ':members'))
                ->setParameter('members', $members)
            ;
        }

        return $query->getQuery()->getResult();
    }
}
