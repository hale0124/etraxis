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

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\CommandBus\Fields\DeleteFieldCommand;
use eTraxis\Entity\Field;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class DeleteFieldCommandHandler
{
    protected $manager;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Deletes specified field.
     *
     * @param   DeleteFieldCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(DeleteFieldCommand $command)
    {
        /** @var Field $entity */
        $entity = $this->manager->getRepository(Field::class)->findOneBy([
            'id'        => $command->id,
            'removedAt' => null,
        ]);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown field.');
        }

        $old_order = $entity->getOrder();

        $entity->remove();

        $this->manager->persist($entity);

        // Reorder remaining fields.
        $fields = $entity->getState()->getFields();

        foreach ($fields as $field) {
            if ($field->getOrder() > $old_order) {
                $field->setOrder($field->getOrder() - 1);
                $this->manager->persist($field);
            }
        }
    }
}
