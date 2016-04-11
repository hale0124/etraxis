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
 * Projects repository.
 */
class ProjectsRepository extends EntityRepository
{
    /**
     * Finds all existing projects.
     *
     * @return  \eTraxis\Entity\Project[]
     */
    public function getProjects()
    {
        $query = $this->createQueryBuilder('p');

        $query
            ->select('p')
            ->orderBy('p.name')
        ;

        return $query->getQuery()->getResult();
    }
}
