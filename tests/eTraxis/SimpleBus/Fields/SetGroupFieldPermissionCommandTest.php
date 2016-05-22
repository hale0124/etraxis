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

use eTraxis\Entity\Field;
use eTraxis\Entity\Group;
use eTraxis\Tests\TransactionalTestCase;

class SetGroupFieldPermissionCommandTest extends TransactionalTestCase
{
    public function testExistingGroupPermissions()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Staff']);

        self::assertEquals(Field::ACCESS_READ_ONLY, $field->getGroupPermission($group));

        $command = new SetGroupFieldPermissionCommand([
            'id'         => $field->getId(),
            'group'      => $group->getId(),
            'permission' => Field::ACCESS_READ_WRITE,
        ]);

        $this->command_bus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        self::assertEquals(Field::ACCESS_READ_WRITE, $field->getGroupPermission($group));
    }

    public function testNewGroupPermissions()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Nimbus']);

        self::assertEquals(Field::ACCESS_DENIED, $field->getGroupPermission($group));

        $command = new SetGroupFieldPermissionCommand([
            'id'         => $field->getId(),
            'group'      => $group->getId(),
            'permission' => Field::ACCESS_READ_WRITE,
        ]);

        $this->command_bus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        self::assertEquals(Field::ACCESS_READ_WRITE, $field->getGroupPermission($group));
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
            'permission' => Field::ACCESS_READ_WRITE,
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
            'permission' => Field::ACCESS_READ_WRITE,
        ]);

        $this->command_bus->handle($command);
    }
}
