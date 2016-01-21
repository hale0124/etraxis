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
 * Templates repository.
 */
class TemplatesRepository extends EntityRepository
{
    /**
     * Finds all templates available for the specified project.
     *
     * @param   int $id Project ID.
     *
     * @return  array
     */
    public function getTemplates($id)
    {
        $repository = $this->getEntityManager()->getRepository('eTraxis:Template');

        $query = $repository->createQueryBuilder('t');

        $query
            ->select('t.id')
            ->addSelect('t.projectId')
            ->addSelect('t.name')
            ->addSelect('t.isLocked')
            ->where('t.projectId = :id')
            ->setParameter('id', $id)
            ->orderBy('t.name')
        ;

        return $query->getQuery()->getResult();
    }
}
