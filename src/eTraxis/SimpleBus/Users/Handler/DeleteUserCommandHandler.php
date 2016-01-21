<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users\Handler;

use eTraxis\Entity\User;
use eTraxis\SimpleBus\Users\DeleteUserCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Command handler.
 */
class DeleteUserCommandHandler
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
     * Deletes specified account.
     *
     * @param   DeleteUserCommand $command
     *
     * @throws  AccessDeniedHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(DeleteUserCommand $command)
    {
        $repository = $this->doctrine->getRepository('eTraxis:User');

        /** @var \eTraxis\Entity\User $entity */
        $entity = $repository->find($command->id);

        if (!$entity) {
            $this->logger->error('Unknown user.', [$command->id]);
            throw new NotFoundHttpException('Unknown user.');
        }

        if (!$this->security->isGranted(User::DELETE, $entity)) {
            throw new AccessDeniedHttpException();
        }

        $this->doctrine->getManager()->remove($entity);
        $this->doctrine->getManager()->flush();
    }
}
