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
use eTraxis\Collection\SystemRole;
use eTraxis\Entity\Template;

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
        $query = $this->createQueryBuilder('t');

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

    /**
     * Returns permissions of specified system role for specified template.
     *
     * @param   int $templateId Template ID.
     * @param   int $role       System role.
     *
     * @return  int
     */
    public function getRolePermissions($templateId, $role)
    {
        /** @var Template $template */
        $template = $this->find($templateId);

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
     * @param   int $templateId Template ID.
     * @param   int $groupId    Group ID.
     *
     * @return  int
     */
    public function getGroupPermissions($templateId, $groupId)
    {
        $repository = $this->getEntityManager()->getRepository('eTraxis:TemplateGroupPermission');

        $query = $repository->createQueryBuilder('tgp');

        $query
            ->select('tgp.permission')
            ->where('tgp.templateId = :template')
            ->andWhere('tgp.groupId = :group')
            ->setParameter('template', $templateId)
            ->setParameter('group', $groupId)
        ;

        $result = $query->getQuery()->getOneOrNullResult();

        return $result === null ? 0 : $result['permission'];
    }
}
