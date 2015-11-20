<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\States\Handler;

use eTraxis\Entity\State;
use eTraxis\SimpleBus\States\DeleteStateCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Command handler.
 */
class DeleteStateCommandHandler
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
     * Deletes specified state.
     *
     * @param   DeleteStateCommand $command
     *
     * @throws  AccessDeniedException
     * @throws  NotFoundHttpException
     */
    public function handle(DeleteStateCommand $command)
    {
        $repository = $this->doctrine->getRepository('eTraxis:State');

        $entity = $repository->find($command->id);

        if (!$entity) {
            $this->logger->error('Unknown state.', [$command->id]);
            throw new NotFoundHttpException('Unknown state.');
        }

        if (!$this->security->isGranted(State::DELETE, $entity)) {
            throw new AccessDeniedException();
        }

        $this->doctrine->getManager()->remove($entity);
        $this->doctrine->getManager()->flush();
    }
}
