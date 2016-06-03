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
use eTraxis\SimpleBus\ListItems\DeleteListItemCommand;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Command handler.
 */
class DeleteListItemCommandHandler
{
    protected $manager;
    protected $security;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface        $manager
     * @param   AuthorizationCheckerInterface $security
     */
    public function __construct(EntityManagerInterface $manager, AuthorizationCheckerInterface $security)
    {
        $this->manager  = $manager;
        $this->security = $security;
    }

    /**
     * Deletes specified list item.
     *
     * @param   DeleteListItemCommand $command
     *
     * @throws  AccessDeniedHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(DeleteListItemCommand $command)
    {
        /** @var Field $field */
        $field = $this->manager->find(Field::class, $command->field);

        if (!$field) {
            throw new NotFoundHttpException('Unknown field.');
        }

        $entity = $this->manager->getRepository(ListItem::class)->findOneBy([
            'field' => $field,
            'value' => $command->value,
        ]);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown list item.');
        }

        if (!$this->security->isGranted(ListItem::DELETE, $entity)) {
            throw new AccessDeniedHttpException();
        }

        $this->manager->remove($entity);
    }
}
