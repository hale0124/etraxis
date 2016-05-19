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
use eTraxis\SimpleBus\Fields\Command\FieldCommand;
use eTraxis\SimpleBus\Fields\Command\UpdateFieldCommandTrait;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler to update existing fields.
 */
class UpdateFieldCommandHandler extends FieldCommandHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(FieldCommand $command)
    {
        if (!in_array(UpdateFieldCommandTrait::class, class_uses($command))) {
            throw new BadRequestHttpException('Unsupported command.');
        }

        /** @var Field $entity */
        /** @noinspection PhpUndefinedFieldInspection */
        $entity = $this->manager->find(Field::class, $command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown field.');
        }

        $entity
            ->setName($command->name)
            ->setDescription($command->description)
            ->setRequired($command->required)
            ->setShowInEmails($command->showInEmails)
        ;

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
