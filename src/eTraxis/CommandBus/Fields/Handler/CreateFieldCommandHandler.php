<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Fields\Handler;

use eTraxis\CommandBus\Fields\Command\CreateFieldCommandTrait;
use eTraxis\CommandBus\Fields\Command\FieldCommand;
use eTraxis\Entity\Field;
use eTraxis\Entity\State;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler to create new fields.
 */
class CreateFieldCommandHandler extends FieldCommandHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(FieldCommand $command)
    {
        if (!in_array(CreateFieldCommandTrait::class, class_uses($command))) {
            throw new BadRequestHttpException('Unsupported command.');
        }

        /** @var State $state */
        /** @noinspection PhpUndefinedFieldInspection */
        $state = $this->manager->find(State::class, $command->state);

        if (!$state) {
            throw new NotFoundHttpException('Unknown state.');
        }

        $entity = new Field($state, self::TYPES[get_parent_class($command)] ?? null);

        $entity
            ->setEntityManager($this->manager)
            ->setName($command->name)
            ->setDescription($command->description)
            ->setRequired($command->required)
        ;

        $entity->setOrder(count($state->getFields()) + 1);

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        $method = self::HANDLERS[get_parent_class($command)] ?? null;

        if ($method !== null) {
            $entity = $this->$method($entity, $command);
        }

        $this->manager->persist($entity);
    }
}
