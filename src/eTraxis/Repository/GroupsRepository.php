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
use eTraxis\Entity\Project;
use eTraxis\Entity\User;

/**
 * Groups repository.
 */
class GroupsRepository extends EntityRepository
{
    /**
     * Finds all groups available for the specified project.
     *
     * @param   Project $project
     *
     * @return  Group[]
     */
    public function getGroups(Project $project)
    {
        $query = $this->createQueryBuilder('g');

        $query
            ->select('g')
            ->where('g.project IS NULL')
            ->orWhere('g.project = :project')
            ->setParameter('project', $project)
            ->orderBy('g.name')
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * Finds all global groups.
     *
     * @return  Group[]
     */
    public function getGlobalGroups()
    {
        $query = $this->createQueryBuilder('g');

        $query
            ->select('g')
            ->where('g.project IS NULL')
            ->orderBy('g.name')
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * Finds all accounts which doesn't belong to the specified group.
     *
     * @param   Group $group
     *
     * @return  User[]
     */
    public function getGroupNonMembers(Group $group)
    {
        $repository = $this->getEntityManager()->getRepository(User::class);

        $query = $repository->createQueryBuilder('u');

        $query
            ->select('u')
            ->orderBy('u.fullname')
            ->addOrderBy('u.username')
        ;

        $members = $group->getMembers();

        if (count($members) > 0) {
            $query
                ->where($query->expr()->notIn('u', ':members'))
                ->setParameter('members', $members)
            ;
        }

        return $query->getQuery()->getResult();
    }
}
