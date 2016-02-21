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

use eTraxis\Entity\Field;
use eTraxis\Entity\State;
use eTraxis\Entity\Template;
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

        $entity
            ->setName($command->name)
            ->setDescription($command->description)
            ->setRequired($command->required)
            ->setGuestAccess($command->guestAccess)
            ->setShowInEmails($command->showInEmails)
            ->setRegisteredAccess(Field::ACCESS_DENIED)
            ->setAuthorAccess(Field::ACCESS_DENIED)
            ->setResponsibleAccess(Field::ACCESS_DENIED)
        ;

        if ($command->state) {
            /** @var State $state */
            $state = $this->doctrine->getRepository(State::class)->find($command->state);

            if (!$state) {
                $this->logger->error('Unknown state.', [$command->state]);
                throw new NotFoundHttpException('Unknown state.');
            }

            $entity->setState($state);
            $entity->setTemplate($state->getTemplate());
            $entity->setIndexNumber($state->getFields()->count() + 1);
        }
        else {
            /** @var Template $template */
            $template = $this->doctrine->getRepository(Template::class)->find($command->template);

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
     * @return  Field
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    private function update(UpdateFieldBaseCommand $command)
    {
        $repository = $this->doctrine->getRepository(Field::class);

        /** @var Field $entity */
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
