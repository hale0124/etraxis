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
use eTraxis\Entity\StateGroupTransition;
use eTraxis\Entity\StateRoleTransition;

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

    /**
     * Returns transitions of specified system role for specified state.
     *
     * @param   int $stateId State ID.
     * @param   int $role    System role.
     *
     * @return  int[] List of state IDs.
     */
    public function getRoleTransitions($stateId, $role)
    {
        $repository = $this->getEntityManager()->getRepository(StateRoleTransition::class);

        $query = $repository->createQueryBuilder('tr');

        $query
            ->select('tr.toStateId')
            ->where('tr.fromStateId = :state')
            ->andWhere('tr.role = :role')
            ->setParameter('state', $stateId)
            ->setParameter('role', $role)
        ;

        $results = [];

        foreach ($query->getQuery()->getResult() as $result) {
            $results[] = $result['toStateId'];
        }

        return $results;
    }

    /**
     * Returns transitions of specified group for specified state.
     *
     * @param   int $stateId State ID.
     * @param   int $groupId Group ID.
     *
     * @return  int[] List of state IDs.
     */
    public function getGroupTransitions($stateId, $groupId)
    {
        $repository = $this->getEntityManager()->getRepository(StateGroupTransition::class);

        $query = $repository->createQueryBuilder('tr');

        $query
            ->select('tr.toStateId')
            ->where('tr.fromStateId = :state')
            ->andWhere('tr.groupId = :group')
            ->setParameter('state', $stateId)
            ->setParameter('group', $groupId)
        ;

        $results = [];

        foreach ($query->getQuery()->getResult() as $result) {
            $results[] = $result['toStateId'];
        }

        return $results;
    }
}
