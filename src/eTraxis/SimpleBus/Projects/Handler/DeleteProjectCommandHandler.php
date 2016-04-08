<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Projects\Handler;

use eTraxis\Entity\Project;
use eTraxis\SimpleBus\Projects\DeleteProjectCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Command handler.
 */
class DeleteProjectCommandHandler
{
    protected $doctrine;
    protected $security;

    /**
     * Dependency Injection constructor.
     *
     * @param   RegistryInterface             $doctrine
     * @param   AuthorizationCheckerInterface $security
     */
    public function __construct(RegistryInterface $doctrine, AuthorizationCheckerInterface $security)
    {
        $this->doctrine = $doctrine;
        $this->security = $security;
    }

    /**
     * Deletes specified project.
     *
     * @param   DeleteProjectCommand $command
     *
     * @throws  AccessDeniedHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(DeleteProjectCommand $command)
    {
        $repository = $this->doctrine->getRepository(Project::class);

        $entity = $repository->find($command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown project.');
        }

        if (!$this->security->isGranted(Project::DELETE, $entity)) {
            throw new AccessDeniedHttpException();
        }

        $this->doctrine->getManager()->remove($entity);
        $this->doctrine->getManager()->flush();
    }
}
