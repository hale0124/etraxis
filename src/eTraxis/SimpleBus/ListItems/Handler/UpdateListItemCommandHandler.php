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

use eTraxis\Entity\ListItem;
use eTraxis\SimpleBus\ListItems\UpdateListItemCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class UpdateListItemCommandHandler
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
     * Updates specified list item.
     *
     * @param   UpdateListItemCommand $command
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(UpdateListItemCommand $command)
    {
        $repository = $this->doctrine->getRepository(ListItem::class);

        /** @var ListItem $entity */
        $entity = $repository->findOneBy([
            'fieldId' => $command->field,
            'key'     => $command->key,
        ]);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown list item.');
        }

        $entity->setValue($command->value);

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
