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
use eTraxis\Dictionary\TemplatePermission;
use eTraxis\Entity\Template;
use eTraxis\Tests\TransactionalTestCase;

class SetRoleTemplatePermissionsCommandTest extends TransactionalTestCase
{
    public function testAnyonePermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);
        $id       = $template->getId();

        self::assertFalse(in_array(TemplatePermission::VIEW_RECORDS, $template->getRolePermissions(SystemRole::ANYONE)));
        self::assertFalse(in_array(TemplatePermission::CREATE_RECORDS, $template->getRolePermissions(SystemRole::ANYONE)));
        self::assertFalse(in_array(TemplatePermission::EDIT_RECORDS, $template->getRolePermissions(SystemRole::ANYONE)));

        $command = new SetRoleTemplatePermissionsCommand([
            'id'          => $id,
            'role'        => SystemRole::ANYONE,
            'permissions' => [
                TemplatePermission::VIEW_RECORDS,
                TemplatePermission::CREATE_RECORDS,
            ],
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertTrue(in_array(TemplatePermission::VIEW_RECORDS, $template->getRolePermissions(SystemRole::ANYONE)));
        self::assertTrue(in_array(TemplatePermission::CREATE_RECORDS, $template->getRolePermissions(SystemRole::ANYONE)));
        self::assertFalse(in_array(TemplatePermission::EDIT_RECORDS, $template->getRolePermissions(SystemRole::ANYONE)));

        $command = new SetRoleTemplatePermissionsCommand([
            'id'          => $id,
            'role'        => SystemRole::ANYONE,
            'permissions' => [
                TemplatePermission::VIEW_RECORDS,
                TemplatePermission::EDIT_RECORDS,
            ],
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertTrue(in_array(TemplatePermission::VIEW_RECORDS, $template->getRolePermissions(SystemRole::ANYONE)));
        self::assertFalse(in_array(TemplatePermission::CREATE_RECORDS, $template->getRolePermissions(SystemRole::ANYONE)));
        self::assertTrue(in_array(TemplatePermission::EDIT_RECORDS, $template->getRolePermissions(SystemRole::ANYONE)));
    }

    public function testAuthorPermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);
        $id       = $template->getId();

        self::assertTrue(in_array(TemplatePermission::EDIT_RECORDS, $template->getRolePermissions(SystemRole::AUTHOR)));
        self::assertTrue(in_array(TemplatePermission::ADD_COMMENTS, $template->getRolePermissions(SystemRole::AUTHOR)));
        self::assertFalse(in_array(TemplatePermission::REOPEN_RECORDS, $template->getRolePermissions(SystemRole::AUTHOR)));

        $command = new SetRoleTemplatePermissionsCommand([
            'id'          => $id,
            'role'        => SystemRole::AUTHOR,
            'permissions' => [
                TemplatePermission::ADD_COMMENTS,
                TemplatePermission::REOPEN_RECORDS,
            ],
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertFalse(in_array(TemplatePermission::EDIT_RECORDS, $template->getRolePermissions(SystemRole::AUTHOR)));
        self::assertTrue(in_array(TemplatePermission::ADD_COMMENTS, $template->getRolePermissions(SystemRole::AUTHOR)));
        self::assertTrue(in_array(TemplatePermission::REOPEN_RECORDS, $template->getRolePermissions(SystemRole::AUTHOR)));
    }

    public function testResponsiblePermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);
        $id       = $template->getId();

        self::assertTrue(in_array(TemplatePermission::ADD_COMMENTS, $template->getRolePermissions(SystemRole::RESPONSIBLE)));
        self::assertTrue(in_array(TemplatePermission::ATTACH_FILES, $template->getRolePermissions(SystemRole::RESPONSIBLE)));
        self::assertFalse(in_array(TemplatePermission::ATTACH_SUBRECORDS, $template->getRolePermissions(SystemRole::RESPONSIBLE)));

        $command = new SetRoleTemplatePermissionsCommand([
            'id'          => $id,
            'role'        => SystemRole::RESPONSIBLE,
            'permissions' => [
                TemplatePermission::ATTACH_FILES,
                TemplatePermission::ATTACH_SUBRECORDS,
            ],
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertFalse(in_array(TemplatePermission::ADD_COMMENTS, $template->getRolePermissions(SystemRole::RESPONSIBLE)));
        self::assertTrue(in_array(TemplatePermission::ATTACH_FILES, $template->getRolePermissions(SystemRole::RESPONSIBLE)));
        self::assertTrue(in_array(TemplatePermission::ATTACH_SUBRECORDS, $template->getRolePermissions(SystemRole::RESPONSIBLE)));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown template.
     */
    public function testNotFoundTemplate()
    {
        $command = new SetRoleTemplatePermissionsCommand([
            'id'          => self::UNKNOWN_ENTITY_ID,
            'role'        => SystemRole::ANYONE,
            'permissions' => [TemplatePermission::VIEW_RECORDS],
        ]);

        $this->command_bus->handle($command);
    }
}
