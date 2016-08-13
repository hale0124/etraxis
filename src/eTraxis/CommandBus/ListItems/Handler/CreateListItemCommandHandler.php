<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\ListItems\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\CommandBus\ListItems\CreateListItemCommand;
use eTraxis\Dictionary\FieldType;
use eTraxis\Entity\Field;
use eTraxis\Entity\ListItem;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class CreateListItemCommandHandler
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
     * Creates new list item.
     *
     * @param   CreateListItemCommand $command
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(CreateListItemCommand $command)
    {
        /** @var Field $field */
        $field = $this->manager->find(Field::class, $command->field);

        if (!$field) {
            throw new NotFoundHttpException('Unknown field.');
        }

        if ($field->getType() === FieldType::LIST) {

            $entity = new ListItem($field);

            $entity
                ->setValue($command->value)
                ->setText($command->text)
            ;

            $errors = $this->validator->validate($entity);

            if (count($errors)) {
                throw new BadRequestHttpException($errors->get(0)->getMessage());
            }

            $this->manager->persist($entity);
        }
    }
}
