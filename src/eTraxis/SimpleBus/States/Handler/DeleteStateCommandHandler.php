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

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\State;
use eTraxis\SimpleBus\States\DeleteStateCommand;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Command handler.
 */
class DeleteStateCommandHandler
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
     * Deletes specified state.
     *
     * @param   DeleteStateCommand $command
     *
     * @throws  AccessDeniedHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(DeleteStateCommand $command)
    {
        $entity = $this->manager->find(State::class, $command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown state.');
        }

        if (!$this->security->isGranted(State::DELETE, $entity)) {
            throw new AccessDeniedHttpException();
        }

        $this->manager->remove($entity);
    }
}
