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
use eTraxis\Entity\Template;
use eTraxis\SimpleBus\Templates\SetRoleTemplatePermissionsCommand ;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class SetRoleTemplatePermissionsCommandHandler
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
     * @param   SetRoleTemplatePermissionsCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(SetRoleTemplatePermissionsCommand $command)
    {
        /** @var Template $template */
        $template = $this->manager->find(Template::class, $command->id);

        if (!$template) {
            throw new NotFoundHttpException('Unknown template.');
        }

        $template->setRolePermissions($command->role, $command->permissions);

        $this->manager->persist($template);
    }
}
