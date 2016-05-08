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
use eTraxis\SimpleBus\Fields\SetRoleFieldPermissionCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class SetRoleFieldPermissionCommandHandler
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
     * Sets permission of specified role to specified field.
     *
     * @param   SetRoleFieldPermissionCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(SetRoleFieldPermissionCommand $command)
    {
        /** @var Field $field */
        $field = $this->manager->getRepository(Field::class)->findOneBy([
            'id'        => $command->id,
            'removedAt' => 0,
        ]);

        if (!$field) {
            throw new NotFoundHttpException('Unknown field.');
        }

        $field->setRolePermission($command->role, $command->permission);

        $this->manager->persist($field);
    }
}
