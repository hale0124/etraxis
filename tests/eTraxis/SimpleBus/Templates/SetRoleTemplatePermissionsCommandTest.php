<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Templates;

use eTraxis\Dictionary\SystemRole;
use eTraxis\Entity\Template;
use eTraxis\Tests\BaseTestCase;

class SetRoleTemplatePermissionsCommandTest extends BaseTestCase
{
    public function testAuthorPermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);
        self::assertNotNull($template);
        $id = $template->getId();

        self::assertEquals(Template::PERMIT_EDIT_RECORD, $template->getRolePermissions(SystemRole::AUTHOR) & Template::PERMIT_EDIT_RECORD);
        self::assertEquals(0,                            $template->getRolePermissions(SystemRole::AUTHOR) & Template::PERMIT_REOPEN_RECORD);

        $command = new SetRoleTemplatePermissionsCommand([
            'id'          => $id,
            'role'        => SystemRole::AUTHOR,
            'permissions' => Template::PERMIT_REOPEN_RECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertEquals(0,                              $template->getRolePermissions(SystemRole::AUTHOR) & Template::PERMIT_EDIT_RECORD);
        self::assertEquals(Template::PERMIT_REOPEN_RECORD, $template->getRolePermissions(SystemRole::AUTHOR) & Template::PERMIT_REOPEN_RECORD);

        $command = new SetRoleTemplatePermissionsCommand([
            'id'          => $id,
            'role'        => SystemRole::AUTHOR,
            'permissions' => Template::PERMIT_EDIT_RECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertEquals(Template::PERMIT_EDIT_RECORD, $template->getRolePermissions(SystemRole::AUTHOR) & Template::PERMIT_EDIT_RECORD);
        self::assertEquals(0,                            $template->getRolePermissions(SystemRole::AUTHOR) & Template::PERMIT_REOPEN_RECORD);
    }

    public function testResponsiblePermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);
        self::assertNotNull($template);
        $id = $template->getId();

        self::assertEquals(Template::PERMIT_ADD_COMMENT, $template->getRolePermissions(SystemRole::RESPONSIBLE) & Template::PERMIT_ADD_COMMENT);
        self::assertEquals(0,                            $template->getRolePermissions(SystemRole::RESPONSIBLE) & Template::PERMIT_ATTACH_SUBRECORD);

        $command = new SetRoleTemplatePermissionsCommand([
            'id'          => $id,
            'role'        => SystemRole::RESPONSIBLE,
            'permissions' => Template::PERMIT_ATTACH_SUBRECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertEquals(0,                                 $template->getRolePermissions(SystemRole::RESPONSIBLE) & Template::PERMIT_ADD_COMMENT);
        self::assertEquals(Template::PERMIT_ATTACH_SUBRECORD, $template->getRolePermissions(SystemRole::RESPONSIBLE) & Template::PERMIT_ATTACH_SUBRECORD);

        $command = new SetRoleTemplatePermissionsCommand([
            'id'          => $id,
            'role'        => SystemRole::RESPONSIBLE,
            'permissions' => Template::PERMIT_ADD_COMMENT,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertEquals(Template::PERMIT_ADD_COMMENT, $template->getRolePermissions(SystemRole::RESPONSIBLE) & Template::PERMIT_ADD_COMMENT);
        self::assertEquals(0,                            $template->getRolePermissions(SystemRole::RESPONSIBLE) & Template::PERMIT_ATTACH_SUBRECORD);
    }

    public function testRegisteredPermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);
        self::assertNotNull($template);
        $id = $template->getId();

        self::assertEquals(0, $template->getRolePermissions(SystemRole::REGISTERED) & Template::PERMIT_VIEW_RECORD);
        self::assertEquals(0, $template->getRolePermissions(SystemRole::REGISTERED) & Template::PERMIT_CREATE_RECORD);

        $command = new SetRoleTemplatePermissionsCommand([
            'id'          => $id,
            'role'        => SystemRole::REGISTERED,
            'permissions' => Template::PERMIT_VIEW_RECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertEquals(Template::PERMIT_VIEW_RECORD,   $template->getRolePermissions(SystemRole::REGISTERED) & Template::PERMIT_VIEW_RECORD);
        self::assertEquals(0,                              $template->getRolePermissions(SystemRole::REGISTERED) & Template::PERMIT_CREATE_RECORD);

        $command = new SetRoleTemplatePermissionsCommand([
            'id'          => $id,
            'role'        => SystemRole::REGISTERED,
            'permissions' => Template::PERMIT_CREATE_RECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertEquals(0,                              $template->getRolePermissions(SystemRole::REGISTERED) & Template::PERMIT_VIEW_RECORD);
        self::assertEquals(Template::PERMIT_CREATE_RECORD, $template->getRolePermissions(SystemRole::REGISTERED) & Template::PERMIT_CREATE_RECORD);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown template.
     */
    public function testNotFoundTemplate()
    {
        $command = new SetRoleTemplatePermissionsCommand([
            'id'          => self::UNKNOWN_ENTITY_ID,
            'role'        => SystemRole::REGISTERED,
            'permissions' => Template::PERMIT_VIEW_RECORD,
        ]);

        $this->command_bus->handle($command);
    }
}
