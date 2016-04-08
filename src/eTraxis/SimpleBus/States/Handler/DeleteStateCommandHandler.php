<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\States\Handler;

use eTraxis\Entity\State;
use eTraxis\SimpleBus\States\DeleteStateCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Command handler.
 */
class DeleteStateCommandHandler
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
     * Deletes specified state.
     *
     * @param   DeleteStateCommand $command
     *
     * @throws  AccessDeniedHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(DeleteStateCommand $command)
    {
        $repository = $this->doctrine->getRepository(State::class);

        $entity = $repository->find($command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown state.');
        }

        if (!$this->security->isGranted(State::DELETE, $entity)) {
            throw new AccessDeniedHttpException();
        }

        $this->doctrine->getManager()->remove($entity);
        $this->doctrine->getManager()->flush();
    }
}
