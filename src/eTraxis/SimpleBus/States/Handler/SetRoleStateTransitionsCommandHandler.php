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
use eTraxis\Entity\StateRoleTransition;
use eTraxis\SimpleBus\States\SetRoleStateTransitionsCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class SetRoleStateTransitionsCommandHandler
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
     * Manages transitions from specified state.
     *
     * @param   SetRoleStateTransitionsCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(SetRoleStateTransitionsCommand $command)
    {
        /** @var State $state */
        $state = $this->manager->find(State::class, $command->id);

        if (!$state) {
            throw new NotFoundHttpException('Unknown state.');
        }

        /** @var State[] $transitions */
        $transitions = $this->manager->getRepository(State::class)->findBy([
            'template' => $state->getTemplate(),
            'id'       => $command->transitions,
        ]);

        $query = $this->manager->createQuery('
            DELETE eTraxis:StateRoleTransition t
            WHERE t.fromState = :state
            AND t.role = :role
        ');

        $query->execute([
            'state' => $state,
            'role'  => $command->role,
        ]);

        foreach ($transitions as $transition) {

            $entity = new StateRoleTransition();

            $entity
                ->setFromState($state)
                ->setToState($transition)
                ->setRole($command->role)
            ;

            $this->manager->persist($entity);
        }
    }
}
