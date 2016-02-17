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

use eTraxis\Entity\Template;
use eTraxis\SimpleBus\Templates\DeleteTemplateCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Command handler.
 */
class DeleteTemplateCommandHandler
{
    protected $logger;
    protected $doctrine;
    protected $security;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface               $logger
     * @param   RegistryInterface             $doctrine
     * @param   AuthorizationCheckerInterface $security
     */
    public function __construct(
        LoggerInterface               $logger,
        RegistryInterface             $doctrine,
        AuthorizationCheckerInterface $security)
    {
        $this->logger   = $logger;
        $this->doctrine = $doctrine;
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
        $repository = $this->doctrine->getRepository(Template::class);

        $entity = $repository->find($command->id);

        if (!$entity) {
            $this->logger->error('Unknown template.', [$command->id]);
            throw new NotFoundHttpException('Unknown template.');
        }

        if (!$this->security->isGranted(Template::DELETE, $entity)) {
            throw new AccessDeniedHttpException();
        }

        $this->doctrine->getManager()->remove($entity);
        $this->doctrine->getManager()->flush();
    }
}
