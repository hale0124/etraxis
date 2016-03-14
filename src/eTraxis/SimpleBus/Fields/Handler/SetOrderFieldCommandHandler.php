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
use eTraxis\SimpleBus\Fields\SetOrderFieldCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class SetOrderFieldCommandHandler
{
    protected $logger;
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface   $logger
     * @param   RegistryInterface $doctrine
     */
    public function __construct(
        LoggerInterface   $logger,
        RegistryInterface $doctrine)
    {
        $this->logger   = $logger;
        $this->doctrine = $doctrine;
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
        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository(Field::class);

        /** @var Field $entity */
        $entity = $repository->findOneBy([
            'id'        => $command->id,
            'removedAt' => 0,
        ]);

        if (!$entity) {
            $this->logger->error('Unknown field.', [$command->id]);
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

        $this->doctrine->getManager()->persist($field);
        $this->doctrine->getManager()->flush();
    }
}
