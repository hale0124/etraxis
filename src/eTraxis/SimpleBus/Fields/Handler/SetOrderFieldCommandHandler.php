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
use eTraxis\SimpleBus\Fields\SetOrderFieldCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class SetOrderFieldCommandHandler
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
     * Sets new order for specified field.
     *
     * @param   SetOrderFieldCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(SetOrderFieldCommand $command)
    {
        /** @var Field $entity */
        $entity = $this->manager->getRepository(Field::class)->findOneBy([
            'id'        => $command->id,
            'removedAt' => 0,
        ]);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown field.');
        }

        $fields = $entity->getState()->getFields();

        $count = count($fields);

        if ($command->order > $count) {
            $command->order = $count;
        }

        $old_order = $entity->getIndexNumber();

        $this->setOrder($entity, 0);

        if ($old_order < $command->order) {
            // Moving the field down.
            for ($i = $old_order; $i < $command->order; $i++) {
                $this->setOrder($fields[$i], $i);
            }
        }
        elseif ($old_order > $command->order) {
            // Moving the field up.
            for ($i = $old_order; $i > $command->order; $i--) {
                $this->setOrder($fields[$i - 2], $i);
            }
        }

        $this->setOrder($entity, $command->order);
    }

    /**
     * Immediately sets new order for specified field.
     *
     * @param   Field $field
     * @param   int   $order
     */
    protected function setOrder(Field $field, $order)
    {
        $field->setIndexNumber($order);

        $this->manager->persist($field);
        $this->manager->flush();
    }
}
