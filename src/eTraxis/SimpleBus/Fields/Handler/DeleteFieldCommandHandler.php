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
use eTraxis\SimpleBus\Fields\DeleteFieldCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class DeleteFieldCommandHandler
{
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
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
        $repository = $this->doctrine->getRepository(Field::class);

        /** @var Field $entity */
        $entity = $repository->findOneBy([
            'id'        => $command->id,
            'removedAt' => 0,
        ]);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown field.');
        }

        $old_order = $entity->getIndexNumber();

        $entity
            ->setIndexNumber(0)
            ->setRemovedAt(time())
        ;

        $this->doctrine->getManager()->persist($entity);

        // Reorder remaining fields.
        $fields = $entity->getState()->getFields();

        foreach ($fields as $field) {
            if ($field->getIndexNumber() > $old_order) {
                $field->setIndexNumber($field->getIndexNumber() - 1);
                $this->doctrine->getManager()->persist($field);
            }
        }

        $this->doctrine->getManager()->flush();
    }
}
