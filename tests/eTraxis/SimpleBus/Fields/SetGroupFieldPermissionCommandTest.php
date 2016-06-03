<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields;

use eTraxis\Dictionary\FieldPermission;
use eTraxis\Entity\Field;
use eTraxis\Entity\Group;
use eTraxis\Tests\TransactionalTestCase;

class SetGroupFieldPermissionCommandTest extends TransactionalTestCase
{
    public function testExtendGroupPermissions()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Staff']);

        self::assertEquals(FieldPermission::READ_ONLY, $field->getGroupPermission($group));

        $command = new SetGroupFieldPermissionCommand([
            'id'         => $field->getId(),
            'group'      => $group->getId(),
            'permission' => FieldPermission::READ_WRITE,
        ]);

        $this->command_bus->handle($command);

        self::assertEquals(FieldPermission::READ_WRITE, $field->getGroupPermission($group));
    }

    public function testRestrictGroupPermissions()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);

        self::assertEquals(FieldPermission::READ_WRITE, $field->getGroupPermission($group));

        $command = new SetGroupFieldPermissionCommand([
            'id'         => $field->getId(),
            'group'      => $group->getId(),
            'permission' => FieldPermission::READ_ONLY,
        ]);

        $this->command_bus->handle($command);

        self::assertEquals(FieldPermission::READ_ONLY, $field->getGroupPermission($group));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown field.
     */
    public function testNotFoundField()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);

        $command = new SetGroupFieldPermissionCommand([
            'id'         => self::UNKNOWN_ENTITY_ID,
            'group'      => $group->getId(),
            'permission' => FieldPermission::READ_WRITE,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown group.
     */
    public function testNotFoundGroup()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Delivery']);

        $command = new SetGroupFieldPermissionCommand([
            'id'         => $field->getId(),
            'group'      => self::UNKNOWN_ENTITY_ID,
            'permission' => FieldPermission::READ_WRITE,
        ]);

        $this->command_bus->handle($command);
    }
}
