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

use eTraxis\Dictionary\SystemRole;
use eTraxis\Entity\Field;
use eTraxis\Tests\TransactionalTestCase;

class SetRoleFieldPermissionCommandTest extends TransactionalTestCase
{
    public function testAuthorPermissions()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        self::assertEquals(Field::ACCESS_READ_WRITE, $field->getRolePermission(SystemRole::AUTHOR));

        $command = new SetRoleFieldPermissionCommand([
            'id'         => $field->getId(),
            'role'       => SystemRole::AUTHOR,
            'permission' => Field::ACCESS_READ_ONLY,
        ]);

        $this->command_bus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->find($command->id);

        self::assertEquals(Field::ACCESS_READ_ONLY, $field->getRolePermission(SystemRole::AUTHOR));
    }

    public function testResponsiblePermissions()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        self::assertEquals(Field::ACCESS_READ_ONLY, $field->getRolePermission(SystemRole::RESPONSIBLE));

        $command = new SetRoleFieldPermissionCommand([
            'id'         => $field->getId(),
            'role'       => SystemRole::RESPONSIBLE,
            'permission' => Field::ACCESS_READ_WRITE,
        ]);

        $this->command_bus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->find($command->id);

        self::assertEquals(Field::ACCESS_READ_WRITE, $field->getRolePermission(SystemRole::RESPONSIBLE));
    }

    public function testRegisteredPermissions()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        self::assertEquals(Field::ACCESS_DENIED, $field->getRolePermission(SystemRole::REGISTERED));

        $command = new SetRoleFieldPermissionCommand([
            'id'         => $field->getId(),
            'role'       => SystemRole::REGISTERED,
            'permission' => Field::ACCESS_READ_ONLY,
        ]);

        $this->command_bus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->find($command->id);

        self::assertEquals(Field::ACCESS_READ_ONLY, $field->getRolePermission(SystemRole::REGISTERED));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown field.
     */
    public function testNotFoundField()
    {
        $command = new SetRoleFieldPermissionCommand([
            'id'         => self::UNKNOWN_ENTITY_ID,
            'role'       => SystemRole::REGISTERED,
            'permission' => Field::ACCESS_READ_WRITE,
        ]);

        $this->command_bus->handle($command);
    }
}
