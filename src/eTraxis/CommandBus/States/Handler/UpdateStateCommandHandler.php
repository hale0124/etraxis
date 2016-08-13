<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\States\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\CommandBus\States\UpdateStateCommand;
use eTraxis\Dictionary\StateResponsible;
use eTraxis\Dictionary\StateType;
use eTraxis\Entity\State;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class UpdateStateCommandHandler
{
    protected $validator;
    protected $manager;

    /**
     * Dependency Injection constructor.
     *
     * @param   ValidatorInterface     $validator
     * @param   EntityManagerInterface $manager
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $manager)
    {
        $this->validator = $validator;
        $this->manager   = $manager;
    }

    /**
     * Updates specified state.
     *
     * @param   UpdateStateCommand $command
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(UpdateStateCommand $command)
    {
        /** @var State $entity */
        $entity = $this->manager->find(State::class, $command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown state.');
        }

        $entity
            ->setName($command->name)
            ->setAbbreviation($command->abbreviation)
            ->setResponsible($entity->getType() === StateType::IS_FINAL ? StateResponsible::REMOVE : $command->responsible)
        ;

        if ($command->nextState) {

            /** @var State $nextState */
            $nextState = $this->manager->find(State::class, $command->nextState);

            if (!$nextState) {
                throw new NotFoundHttpException('Unknown next state.');
            }

            $entity->setNextState($nextState);
        }

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        $this->manager->persist($entity);
    }
}
