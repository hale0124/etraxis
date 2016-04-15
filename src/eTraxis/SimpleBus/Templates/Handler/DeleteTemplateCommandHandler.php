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
use eTraxis\SimpleBus\Templates\DeleteTemplateCommand;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Command handler.
 */
class DeleteTemplateCommandHandler
{
    protected $manager;
    protected $security;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface        $manager
     * @param   AuthorizationCheckerInterface $security
     */
    public function __construct(EntityManagerInterface $manager, AuthorizationCheckerInterface $security)
    {
        $this->manager  = $manager;
        $this->security = $security;
    }

    /**
     * Deletes specified template.
     *
     * @param   DeleteTemplateCommand $command
     *
     * @throws  AccessDeniedHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(DeleteTemplateCommand $command)
    {
        $entity = $this->manager->find(Template::class, $command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown template.');
        }

        if (!$this->security->isGranted(Template::DELETE, $entity)) {
            throw new AccessDeniedHttpException();
        }

        $this->manager->remove($entity);
        $this->manager->flush();
    }
}
