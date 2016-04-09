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
use eTraxis\Entity\State;
use eTraxis\Entity\StateGroupTransition;
use eTraxis\Entity\StateRoleTransition;

/**
 * States repository.
 */
class StatesRepository extends EntityRepository
{
    /**
     * Returns transitions of specified system role for specified state.
     *
     * @param   State $state
     * @param   int   $role
     *
     * @return  State[] List of states.
     */
    public function getRoleTransitions(State $state, $role)
    {
        $repository = $this->getEntityManager()->getRepository(StateRoleTransition::class);

        $query = $repository->createQueryBuilder('tr');

        $query
            ->select('tr')
            ->where('tr.fromState = :state')
            ->andWhere('tr.role = :role')
            ->setParameter('state', $state)
            ->setParameter('role', $role)
        ;

        $results = [];

        /** @var StateRoleTransition $result */
        foreach ($query->getQuery()->getResult() as $result) {
            $results[] = $result->getToState();
        }

        return $results;
    }

    /**
     * Returns transitions of specified group for specified state.
     *
     * @param   State $state
     * @param   Group $group
     *
     * @return  State[] List of states.
     */
    public function getGroupTransitions(State $state, Group $group)
    {
        $repository = $this->getEntityManager()->getRepository(StateGroupTransition::class);

        $query = $repository->createQueryBuilder('tr');

        $query
            ->select('tr')
            ->where('tr.fromState = :state')
            ->andWhere('tr.group = :group')
            ->setParameter('state', $state)
            ->setParameter('group', $group)
        ;

        $results = [];

        /** @var StateGroupTransition $result */
        foreach ($query->getQuery()->getResult() as $result) {
            $results[] = $result->getToState();
        }

        return $results;
    }
}
