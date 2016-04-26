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
 * Groups repository.
 */
class GroupsRepository extends EntityRepository
{
    /**
     * Finds all global groups.
     *
     * @return  \eTraxis\Entity\Group[]
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
}
