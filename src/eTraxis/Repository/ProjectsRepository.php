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
 * Projects repository.
 */
class ProjectsRepository extends EntityRepository
{
    /**
     * Finds all existing projects.
     *
     * @return  array
     */
    public function getProjects()
    {
        $repository = $this->getEntityManager()->getRepository('eTraxis:Project');

        $query = $repository->createQueryBuilder('p');

        $query
            ->select('p.id')
            ->addSelect('p.name')
            ->orderBy('p.name')
        ;

        return $query->getQuery()->getResult();
    }
}
