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
use eTraxis\Dictionary\StateType;
use eTraxis\Entity\State;
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

        // Transitions are not applicable for final states.
        if ($state->getType() !== StateType::IS_FINAL) {

            /** @var State[] $transitions */
            $transitions = $this->manager->getRepository(State::class)->findBy([
                'template' => $state->getTemplate(),
                'id'       => $command->transitions,
            ]);

            $state->setRoleTransitions($command->role, $transitions);

            $this->manager->persist($state);
        }
    }
}
