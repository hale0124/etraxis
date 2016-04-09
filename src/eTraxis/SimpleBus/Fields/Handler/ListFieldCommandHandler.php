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
use eTraxis\Entity\ListItem;
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
     * @throws  NotFoundHttpException
     */
    public function handle($command)
    {
        $entity = $this->getEntity($command);

        $entity->setType(Field::TYPE_LIST);

        if ($command instanceof UpdateListFieldCommand) {

            $repository = $this->doctrine->getRepository(ListItem::class);

            if ($command->defaultValue !== null) {

                /** @var ListItem $item */
                $item = $repository->findOneBy([
                    'field' => $entity,
                    'key'   => $command->defaultValue,
                ]);

                if (!$item) {
                    throw new NotFoundHttpException('Unknown list item.');
                }
            }

            $entity->getParameters()->setDefaultValue($command->defaultValue);
        }

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
