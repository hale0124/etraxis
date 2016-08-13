<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Fields;

use eTraxis\Dictionary\FieldPermission;
use eTraxis\Dictionary\SystemRole;
use eTraxis\Entity\Field;
use eTraxis\Tests\TransactionalTestCase;

class SetRoleFieldPermissionCommandTest extends TransactionalTestCase
{
    public function testAnyonePermissions()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        self::assertEquals(FieldPermission::NONE, $field->getRolePermission(SystemRole::ANYONE));

        $command = new SetRoleFieldPermissionCommand([
            'id'         => $field->getId(),
            'role'       => SystemRole::ANYONE,
            'permission' => FieldPermission::READ_ONLY,
        ]);

        $this->commandbus->handle($command);

        self::assertEquals(FieldPermission::READ_ONLY, $field->getRolePermission(SystemRole::ANYONE));
    }

    public function testAuthorPermissions()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        self::assertEquals(FieldPermission::READ_WRITE, $field->getRolePermission(SystemRole::AUTHOR));

        $command = new SetRoleFieldPermissionCommand([
            'id'         => $field->getId(),
            'role'       => SystemRole::AUTHOR,
            'permission' => FieldPermission::READ_ONLY,
        ]);

        $this->commandbus->handle($command);

        self::assertEquals(FieldPermission::READ_ONLY, $field->getRolePermission(SystemRole::AUTHOR));
    }

    public function testResponsiblePermissions()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        self::assertEquals(FieldPermission::READ_ONLY, $field->getRolePermission(SystemRole::RESPONSIBLE));

        $command = new SetRoleFieldPermissionCommand([
            'id'         => $field->getId(),
            'role'       => SystemRole::RESPONSIBLE,
            'permission' => FieldPermission::READ_WRITE,
        ]);

        $this->commandbus->handle($command);

        self::assertEquals(FieldPermission::READ_WRITE, $field->getRolePermission(SystemRole::RESPONSIBLE));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown field.
     */
    public function testNotFoundField()
    {
        $command = new SetRoleFieldPermissionCommand([
            'id'         => self::UNKNOWN_ENTITY_ID,
            'role'       => SystemRole::ANYONE,
            'permission' => FieldPermission::READ_WRITE,
        ]);

        $this->commandbus->handle($command);
    }
}
