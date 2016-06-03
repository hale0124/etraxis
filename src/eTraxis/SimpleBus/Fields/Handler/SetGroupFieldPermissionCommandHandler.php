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
use eTraxis\Entity\Group;
use eTraxis\SimpleBus\Fields\SetGroupFieldPermissionCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class SetGroupFieldPermissionCommandHandler
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
     * Sets permission of specified group to specified field.
     *
     * @param   SetGroupFieldPermissionCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(SetGroupFieldPermissionCommand $command)
    {
        /** @var Field $field */
        $field = $this->manager->getRepository(Field::class)->findOneBy([
            'id'        => $command->id,
            'removedAt' => null,
        ]);

        if (!$field) {
            throw new NotFoundHttpException('Unknown field.');
        }

        /** @var Group $group */
        $group = $this->manager->find(Group::class, $command->group);

        if (!$group) {
            throw new NotFoundHttpException('Unknown group.');
        }

        $field->setGroupPermission($group, $command->permission);

        $this->manager->persist($field);
    }
}
