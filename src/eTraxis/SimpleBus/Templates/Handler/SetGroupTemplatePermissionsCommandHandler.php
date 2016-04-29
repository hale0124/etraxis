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

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\Group;
use eTraxis\Entity\Template;
use eTraxis\Entity\TemplateGroupPermission;
use eTraxis\SimpleBus\Templates\SetGroupTemplatePermissionsCommand ;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class SetGroupTemplatePermissionsCommandHandler
{
    protected $manager;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Sets permissions to specified template.
     *
     * @param   SetGroupTemplatePermissionsCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(SetGroupTemplatePermissionsCommand $command)
    {
        /** @var Template $template */
        $template = $this->manager->find(Template::class, $command->id);

        if (!$template) {
            throw new NotFoundHttpException('Unknown template.');
        }

        /** @var Group $group */
        $group = $this->manager->find(Group::class, $command->group);

        if (!$group) {
            throw new NotFoundHttpException('Unknown group.');
        }

        /** @var TemplateGroupPermission $entity */
        $entity = $this->manager->getRepository(TemplateGroupPermission::class)->findOneBy([
            'group'    => $group,
            'template' => $template,
        ]);

        if (!$entity) {
            $entity = new TemplateGroupPermission();

            $entity->setGroup($group);
            $entity->setTemplate($template);
        }

        $entity->setPermission($command->permissions);

        $this->manager->persist($entity);
    }
}
