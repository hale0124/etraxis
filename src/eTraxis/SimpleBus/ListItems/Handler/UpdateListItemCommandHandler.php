<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\ListItems\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\Field;
use eTraxis\Entity\ListItem;
use eTraxis\SimpleBus\ListItems\UpdateListItemCommand;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class UpdateListItemCommandHandler
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
     * Updates specified list item.
     *
     * @param   UpdateListItemCommand $command
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(UpdateListItemCommand $command)
    {
        /** @var Field $field */
        $field = $this->manager->find(Field::class, $command->field);

        if (!$field) {
            throw new NotFoundHttpException('Unknown field.');
        }

        /** @var ListItem $entity */
        $entity = $this->manager->getRepository(ListItem::class)->findOneBy([
            'field' => $field,
            'key'   => $command->key,
        ]);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown list item.');
        }

        $entity->setValue($command->value);

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        $this->manager->persist($entity);
    }
}
