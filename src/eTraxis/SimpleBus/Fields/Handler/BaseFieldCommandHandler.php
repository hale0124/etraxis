<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\Field;
use eTraxis\Entity\State;
use eTraxis\SimpleBus\Fields\CreateFieldBaseCommand;
use eTraxis\SimpleBus\Fields\UpdateFieldBaseCommand;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Base command handler to create/update fields.
 */
class BaseFieldCommandHandler
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
     * Returns a field entity for create or update operation.
     *
     * @param   CreateFieldBaseCommand|UpdateFieldBaseCommand $command
     *
     * @return  Field
     *
     * @throws  BadRequestHttpException
     */
    protected function getEntity($command)
    {
        if ($command instanceof CreateFieldBaseCommand) {
            return $this->create($command);
        }

        if ($command instanceof UpdateFieldBaseCommand) {
            return $this->update($command);
        }

        throw new BadRequestHttpException('Unsupported command.');
    }

    /**
     * Creates new field.
     *
     * @param   CreateFieldBaseCommand $command
     *
     * @return  Field
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    private function create(CreateFieldBaseCommand $command)
    {
        $entity = new Field();

        /** @noinspection PhpParamsInspection */
        $entity
            ->injectDependencies($this->manager)
            ->setName($command->name)
            ->setDescription($command->description)
            ->setRequired($command->required)
            ->setGuestAccess($command->guestAccess)
            ->setShowInEmails($command->showInEmails)
            ->setRegisteredAccess(Field::ACCESS_DENIED)
            ->setAuthorAccess(Field::ACCESS_DENIED)
            ->setResponsibleAccess(Field::ACCESS_DENIED)
        ;

        /** @var State $state */
        $state = $this->manager->find(State::class, $command->state);

        if (!$state) {
            throw new NotFoundHttpException('Unknown state.');
        }

        $entity->setState($state);
        $entity->setIndexNumber(count($state->getFields()) + 1);

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        return $entity;
    }

    /**
     * Updates existing field.
     *
     * @param   UpdateFieldBaseCommand $command
     *
     * @return  Field
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    private function update(UpdateFieldBaseCommand $command)
    {
        /** @var Field $entity */
        $entity = $this->manager->find(Field::class, $command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown field.');
        }

        $entity
            ->setName($command->name)
            ->setDescription($command->description)
            ->setRequired($command->required)
            ->setGuestAccess($command->guestAccess)
            ->setShowInEmails($command->showInEmails)
        ;

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        return $entity;
    }
}
