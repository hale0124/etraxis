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

use eTraxis\Entity;
use eTraxis\SimpleBus\Fields\CreateFieldBaseCommand;
use eTraxis\SimpleBus\Fields\UpdateFieldBaseCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Base command handler to create/update fields.
 */
class BaseFieldCommandHandler
{
    protected $logger;
    protected $validator;
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface    $logger
     * @param   ValidatorInterface $validator
     * @param   RegistryInterface  $doctrine
     */
    public function __construct(
        LoggerInterface    $logger,
        ValidatorInterface $validator,
        RegistryInterface  $doctrine)
    {
        $this->logger    = $logger;
        $this->validator = $validator;
        $this->doctrine  = $doctrine;
    }

    /**
     * Returns a field entity for create or update operation.
     *
     * @param   CreateFieldBaseCommand|UpdateFieldBaseCommand $command
     *
     * @return  Entity\Field
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
     * @return  Entity\Field
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    private function create(CreateFieldBaseCommand $command)
    {
        $entity = new Entity\Field();

        /** @noinspection PhpParamsInspection */
        $entity
            ->setName($command->name)
            ->setDescription($command->description)
            ->setRequired($command->required)
            ->setGuestAccess($command->guestAccess)
            ->setShowInEmails($command->showInEmails)
            ->setRegisteredAccess(Entity\Field::ACCESS_DENIED)
            ->setAuthorAccess(Entity\Field::ACCESS_DENIED)
            ->setResponsibleAccess(Entity\Field::ACCESS_DENIED)
            ->setDecimalValuesRepository($this->doctrine->getRepository(Entity\DecimalValue::class))
            ->setStringValuesRepository($this->doctrine->getRepository(Entity\StringValue::class))
            ->setTextValuesRepository($this->doctrine->getRepository(Entity\TextValue::class))
            ->setListItemsRepository($this->doctrine->getRepository(Entity\ListItem::class))
        ;

        if ($command->state) {
            /** @var Entity\State $state */
            $state = $this->doctrine->getRepository(Entity\State::class)->find($command->state);

            if (!$state) {
                $this->logger->error('Unknown state.', [$command->state]);
                throw new NotFoundHttpException('Unknown state.');
            }

            $entity->setState($state);
            $entity->setTemplate($state->getTemplate());
            $entity->setIndexNumber($state->getFields()->count() + 1);
        }
        else {
            /** @var Entity\Template $template */
            $template = $this->doctrine->getRepository(Entity\Template::class)->find($command->template);

            if (!$template) {
                $this->logger->error('Unknown template.', [$command->template]);
                throw new NotFoundHttpException('Unknown template.');
            }

            $entity->setTemplate($template);
            $entity->setIndexNumber($template->getFields()->count() + 1);
        }

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            $message = $errors->get(0)->getMessage();
            $this->logger->error($message);
            throw new BadRequestHttpException($message);
        }

        return $entity;
    }

    /**
     * Updates existing field.
     *
     * @param   UpdateFieldBaseCommand $command
     *
     * @return  Entity\Field
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    private function update(UpdateFieldBaseCommand $command)
    {
        $repository = $this->doctrine->getRepository(Entity\Field::class);

        /** @var Entity\Field $entity */
        $entity = $repository->find($command->id);

        if (!$entity) {
            $this->logger->error('Unknown field.', [$command->id]);
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
            $message = $errors->get(0)->getMessage();
            $this->logger->error($message);
            throw new BadRequestHttpException($message);
        }

        return $entity;
    }
}
