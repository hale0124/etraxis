<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Users\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\CommandBus\Users\DeleteUserCommand;
use eTraxis\Entity\User;
use eTraxis\Voter\UserVoter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Command handler.
 */
class DeleteUserCommandHandler
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
     * Deletes specified account.
     *
     * @param   DeleteUserCommand $command
     *
     * @throws  AccessDeniedHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(DeleteUserCommand $command)
    {
        /** @var User $entity */
        $entity = $this->manager->find(User::class, $command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown user.');
        }

        if (!$this->security->isGranted(UserVoter::DELETE, $entity)) {
            throw new AccessDeniedHttpException();
        }

        $this->manager->remove($entity);
    }
}
