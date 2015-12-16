<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields\Handler;

use eTraxis\Entity\Field;
use eTraxis\SimpleBus\CommandException;
use eTraxis\SimpleBus\Fields\CreateListFieldCommand;
use eTraxis\SimpleBus\Fields\UpdateListFieldCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class ListFieldCommandHandler extends BaseFieldCommandHandler
{
    /**
     * Creates or updates "list" field.
     *
     * @param   CreateListFieldCommand|UpdateListFieldCommand $command
     *
     * @throws  CommandException
     * @throws  NotFoundHttpException
     */
    public function handle($command)
    {
        $entity = $this->getEntity($command);

        $entity
            ->setType(Field::TYPE_LIST)
            ->setParameter1(null)
            ->setParameter2(null)
        ;

        if ($command instanceof UpdateListFieldCommand) {

            $repository = $this->doctrine->getRepository('eTraxis:ListItem');

            /** @var \eTraxis\Entity\ListItem $item */
            $item = $repository->findOneBy([
                'fieldId' => $command->id,
                'key'     => $command->default,
            ]);

            if (!$item) {
                $this->logger->error('Unknown list item.', [$command->id, $command->default]);
                throw new NotFoundHttpException('Unknown list item.');
            }

            $entity->setDefaultValue($command->default);
        }

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
