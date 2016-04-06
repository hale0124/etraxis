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
use eTraxis\SimpleBus\States\UpdateStateCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class UpdateStateCommandHandler
{
    protected $validator;
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   ValidatorInterface $validator
     * @param   RegistryInterface  $doctrine
     */
    public function __construct(ValidatorInterface $validator, RegistryInterface $doctrine)
    {
        $this->validator = $validator;
        $this->doctrine  = $doctrine;
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
        $repository = $this->doctrine->getRepository(State::class);

        /** @var State $entity */
        $entity = $repository->find($command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown state.');
        }

        $entity
            ->setName($command->name)
            ->setAbbreviation($command->abbreviation)
            ->setResponsible($entity->getType() === State::TYPE_FINAL ? State::RESPONSIBLE_REMOVE : $command->responsible)
        ;

        if ($command->nextState) {

            /** @var State $nextState */
            $nextState = $repository->find($command->nextState);

            if (!$nextState) {
                throw new NotFoundHttpException('Unknown next state.');
            }

            $entity->setNextState($nextState);
        }

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
