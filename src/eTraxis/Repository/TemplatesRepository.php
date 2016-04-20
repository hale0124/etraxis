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
use eTraxis\Dictionary\SystemRole;
use eTraxis\Entity\Group;
use eTraxis\Entity\Template;
use eTraxis\Entity\TemplateGroupPermission;

/**
 * Templates repository.
 */
class TemplatesRepository extends EntityRepository
{
    /**
     * Returns permissions of specified system role for specified template.
     *
     * @param   Template  $template
     * @param   int       $role     System role.
     *
     * @return  int
     */
    public function getRolePermissions(Template $template, $role)
    {
        switch ($role) {

            case SystemRole::AUTHOR:
                $permissions = $template->getAuthorPermissions();
                $permissions |= Template::PERMIT_VIEW_RECORD;
                $permissions &= ~Template::PERMIT_CREATE_RECORD;
                break;

            case SystemRole::RESPONSIBLE:
                $permissions = $template->getResponsiblePermissions();
                $permissions |= Template::PERMIT_VIEW_RECORD;
                $permissions &= ~Template::PERMIT_CREATE_RECORD;
                break;

            case SystemRole::REGISTERED:
                $permissions = $template->getRegisteredPermissions();
                break;

            default:
                $permissions = 0;
        }

        return $permissions;
    }

    /**
     * Returns permissions of specified group for specified template.
     *
     * @param   Template  $template
     * @param   Group     $group
     *
     * @return  int
     */
    public function getGroupPermissions(Template $template, Group $group)
    {
        $repository = $this->getEntityManager()->getRepository(TemplateGroupPermission::class);

        $query = $repository->createQueryBuilder('tgp');

        $query
            ->select('tgp.permission')
            ->where('tgp.template = :template')
            ->andWhere('tgp.group = :group')
            ->setParameter('template', $template)
            ->setParameter('group', $group)
        ;

        $result = $query->getQuery()->getOneOrNullResult();

        return $result === null ? 0 : $result['permission'];
    }
}
