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
use Doctrine\ORM\Query\Expr\Join;
use eTraxis\Dictionary;
use eTraxis\Entity\Template;

/**
 * Templates repository.
 */
class TemplatesRepository extends EntityRepository
{
    /**
     * Returns list of templates which records specified user is allowed to see.
     *
     * @param   int $userId
     *
     * @return  Template[]
     */
    public function getTemplates(int $userId)
    {
        // Get all templates allowed to anyone.
        $query = $this->createQueryBuilder('t')
            ->select('t')
            ->innerJoin('t.rolePermissions', 'rp', Join::WITH, 'rp.permission = :permission')
            ->where('rp.role = :role')
        ;

        $templatesByRole = $query->getQuery()->execute([
            'permission' => Dictionary\TemplatePermission::VIEW_RECORDS,
            'role'       => Dictionary\SystemRole::ANYONE,
        ]);

        // Get all templates allowed to user's groups.
        $query = $this->createQueryBuilder('t')
            ->select('t')
            ->innerJoin('t.groupPermissions', 'gp', Join::WITH, 'gp.permission = :permission')
            ->innerJoin('gp.group', 'g')
            ->innerJoin('g.members', 'u', Join::WITH, 'u = :user')
        ;

        $templatesByGroup = $query->getQuery()->execute([
            'permission' => Dictionary\TemplatePermission::VIEW_RECORDS,
            'user'       => $userId,
        ]);

        // Merge results.
        $templates = array_merge($templatesByRole, $templatesByGroup);

        return array_unique($templates);
    }
}
