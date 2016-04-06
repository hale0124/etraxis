<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Templates\Handler;

use eTraxis\Collection\SystemRole;
use eTraxis\Entity\Group;
use eTraxis\Entity\Template;
use eTraxis\Entity\TemplateGroupPermission;
use eTraxis\SimpleBus\Templates\AddTemplatePermissionsCommand;
use eTraxis\SimpleBus\Templates\RemoveTemplatePermissionsCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class AddRemoveTemplatePermissionsCommandHandler
{
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Manages permissions to specified template.
     *
     * @param   AddTemplatePermissionsCommand|RemoveTemplatePermissionsCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle($command)
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->find($command->id);

        if (!$template) {
            throw new NotFoundHttpException('Unknown template.');
        }

        switch ($command->group) {

            case SystemRole::AUTHOR:

                $permissions = $template->getAuthorPermissions();
                $permissions = $this->permissions($command, $permissions);
                $template->setAuthorPermissions($permissions | Template::PERMIT_VIEW_RECORD);
                $this->doctrine->getManager()->persist($template);

                break;

            case SystemRole::RESPONSIBLE:

                $permissions = $template->getResponsiblePermissions();
                $permissions = $this->permissions($command, $permissions);
                $template->setResponsiblePermissions($permissions | Template::PERMIT_VIEW_RECORD);
                $this->doctrine->getManager()->persist($template);

                break;

            case SystemRole::REGISTERED:

                $permissions = $template->getRegisteredPermissions();
                $permissions = $this->permissions($command, $permissions);
                $template->setRegisteredPermissions($permissions);
                $this->doctrine->getManager()->persist($template);

                break;

            default:

                /** @var Group $group */
                $group = $this->doctrine->getRepository(Group::class)->find($command->group);

                if (!$group) {
                    throw new NotFoundHttpException('Unknown group.');
                }

                /** @var TemplateGroupPermission $entity */
                $entity = $this->doctrine->getRepository(TemplateGroupPermission::class)->findOneBy([
                    'groupId'    => $group->getId(),
                    'templateId' => $template->getId(),
                ]);

                if (!$entity) {
                    $entity = new TemplateGroupPermission();

                    $entity->setGroupId($group->getId());
                    $entity->setTemplateId($template->getId());
                    $entity->setGroup($group);
                    $entity->setTemplate($template);
                }

                $permissions = $entity->getPermission();
                $permissions = $this->permissions($command, $permissions);
                $entity->setPermission($permissions);
                $this->doctrine->getManager()->persist($entity);
        }

        $this->doctrine->getManager()->flush();
    }

    /**
     * Alters provided permissions.
     *
     * @param   mixed $command
     * @param   int   $permissions
     *
     * @return  int
     */
    public function permissions($command, $permissions)
    {
        if ($command instanceof AddTemplatePermissionsCommand) {
            return $permissions | $command->permissions;
        }
        elseif ($command instanceof RemoveTemplatePermissionsCommand) {
            return $permissions & ~$command->permissions;
        }

        // @codeCoverageIgnoreStart
        return $permissions;
        // @codeCoverageIgnoreEnd
    }
}
