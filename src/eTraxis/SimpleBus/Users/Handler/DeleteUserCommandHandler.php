<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users\Handler;

use eTraxis\SimpleBus\Users\DeleteUserCommand;
use eTraxis\Voter\UserVoter;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Deletes specified account.
 */
class DeleteUserCommandHandler
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
     * {@inheritdoc}
     */
    public function handle(DeleteUserCommand $command)
    {
        $repository = $this->doctrine->getRepository('eTraxis:User');

        /** @var \eTraxis\Entity\User $entity */
        $entity = $repository->find($command->id);

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        if (!$this->security->isGranted(UserVoter::DELETE, $entity)) {
            throw new AccessDeniedException();
        }

        $this->doctrine->getManager()->remove($entity);
        $this->doctrine->getManager()->flush();
    }
}
