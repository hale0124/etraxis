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
use eTraxis\Entity\Group;
use eTraxis\Entity\Template;
use eTraxis\Entity\TemplateGroupPermission;
use eTraxis\Tests\BaseTestCase;

class AddRemoveTemplatePermissionsCommandTest extends BaseTestCase
{
    public function testExistingGroupPermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);
        self::assertNotNull($template);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);
        self::assertNotNull($group);

        /** @var TemplateGroupPermission $permissions */
        $permissions = $this->doctrine->getRepository(TemplateGroupPermission::class)->findOneBy([
            'group'    => $group,
            'template' => $template,
        ]);
        self::assertNotNull($permissions);

        self::assertEquals(Template::PERMIT_ADD_FILE,    $permissions->getPermission() & Template::PERMIT_ADD_FILE);
        self::assertEquals(Template::PERMIT_REMOVE_FILE, $permissions->getPermission() & Template::PERMIT_REMOVE_FILE);
        self::assertEquals(0,                            $permissions->getPermission() & Template::PERMIT_ATTACH_SUBRECORD);
        self::assertEquals(0,                            $permissions->getPermission() & Template::PERMIT_DETACH_SUBRECORD);

        $command = new AddTemplatePermissionsCommand([
            'id'          => $template->getId(),
            'group'       => $group->getId(),
            'permissions' => Template::PERMIT_ATTACH_SUBRECORD | Template::PERMIT_DETACH_SUBRECORD,
        ]);

        $this->command_bus->handle($command);

        $command = new RemoveTemplatePermissionsCommand([
            'id'          => $template->getId(),
            'group'       => $group->getId(),
            'permissions' => Template::PERMIT_ADD_FILE | Template::PERMIT_REMOVE_FILE,
        ]);

        $this->command_bus->handle($command);

        $permissions = $this->doctrine->getRepository(TemplateGroupPermission::class)->findOneBy([
            'group'    => $group,
            'template' => $template,
        ]);

        self::assertEquals(0,                                 $permissions->getPermission() & Template::PERMIT_ADD_FILE);
        self::assertEquals(0,                                 $permissions->getPermission() & Template::PERMIT_REMOVE_FILE);
        self::assertEquals(Template::PERMIT_ATTACH_SUBRECORD, $permissions->getPermission() & Template::PERMIT_ATTACH_SUBRECORD);
        self::assertEquals(Template::PERMIT_DETACH_SUBRECORD, $permissions->getPermission() & Template::PERMIT_DETACH_SUBRECORD);
    }

    public function testNewGroupPermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Futurama']);
        self::assertNotNull($template);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);
        self::assertNotNull($group);

        /** @var TemplateGroupPermission $permissions */
        $permissions = $this->doctrine->getRepository(TemplateGroupPermission::class)->findOneBy([
            'group'    => $group,
            'template' => $template,
        ]);
        self::assertNull($permissions);

        $command = new AddTemplatePermissionsCommand([
            'id'          => $template->getId(),
            'group'       => $group->getId(),
            'permissions' => Template::PERMIT_VIEW_RECORD,
        ]);

        $this->command_bus->handle($command);

        $permissions = $this->doctrine->getRepository(TemplateGroupPermission::class)->findOneBy([
            'group'    => $group,
            'template' => $template,
        ]);
        self::assertNotNull($permissions);

        self::assertEquals(Template::PERMIT_VIEW_RECORD, $permissions->getPermission() & Template::PERMIT_VIEW_RECORD);
    }

    public function testRegisteredPermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);
        self::assertNotNull($template);
        $id = $template->getId();

        self::assertEquals(0, $template->getRegisteredPermissions() & Template::PERMIT_VIEW_RECORD);
        self::assertEquals(0, $template->getRegisteredPermissions() & Template::PERMIT_CREATE_RECORD);

        $command = new AddTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => SystemRole::REGISTERED,
            'permissions' => Template::PERMIT_VIEW_RECORD | Template::PERMIT_CREATE_RECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertEquals(Template::PERMIT_VIEW_RECORD,   $template->getRegisteredPermissions() & Template::PERMIT_VIEW_RECORD);
        self::assertEquals(Template::PERMIT_CREATE_RECORD, $template->getRegisteredPermissions() & Template::PERMIT_CREATE_RECORD);

        $command = new RemoveTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => SystemRole::REGISTERED,
            'permissions' => Template::PERMIT_CREATE_RECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertEquals(Template::PERMIT_VIEW_RECORD, $template->getRegisteredPermissions() & Template::PERMIT_VIEW_RECORD);
        self::assertEquals(0,                            $template->getRegisteredPermissions() & Template::PERMIT_CREATE_RECORD);
    }

    public function testAuthorPermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);
        self::assertNotNull($template);
        $id = $template->getId();

        self::assertEquals(Template::PERMIT_EDIT_RECORD, $template->getAuthorPermissions() & Template::PERMIT_EDIT_RECORD);
        self::assertEquals(0,                            $template->getAuthorPermissions() & Template::PERMIT_REOPEN_RECORD);

        $command = new AddTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => SystemRole::AUTHOR,
            'permissions' => Template::PERMIT_REOPEN_RECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertEquals(Template::PERMIT_REOPEN_RECORD, $template->getAuthorPermissions() & Template::PERMIT_REOPEN_RECORD);

        $command = new RemoveTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => SystemRole::AUTHOR,
            'permissions' => Template::PERMIT_EDIT_RECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertEquals(0, $template->getAuthorPermissions() & Template::PERMIT_EDIT_RECORD);

        $command = new RemoveTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => SystemRole::AUTHOR,
            'permissions' => Template::PERMIT_VIEW_RECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertEquals(Template::PERMIT_VIEW_RECORD, $template->getAuthorPermissions() & Template::PERMIT_VIEW_RECORD);
    }

    public function testResponsiblePermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);
        self::assertNotNull($template);
        $id = $template->getId();

        self::assertEquals(Template::PERMIT_ADD_COMMENT, $template->getResponsiblePermissions() & Template::PERMIT_ADD_COMMENT);
        self::assertEquals(0,                            $template->getResponsiblePermissions() & Template::PERMIT_ATTACH_SUBRECORD);

        $command = new AddTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => SystemRole::RESPONSIBLE,
            'permissions' => Template::PERMIT_ATTACH_SUBRECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertEquals(Template::PERMIT_ATTACH_SUBRECORD, $template->getResponsiblePermissions() & Template::PERMIT_ATTACH_SUBRECORD);

        $command = new RemoveTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => SystemRole::RESPONSIBLE,
            'permissions' => Template::PERMIT_ADD_COMMENT,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertEquals(0, $template->getResponsiblePermissions() & Template::PERMIT_ADD_COMMENT);

        $command = new RemoveTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => SystemRole::RESPONSIBLE,
            'permissions' => Template::PERMIT_VIEW_RECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->find($id);

        self::assertEquals(Template::PERMIT_VIEW_RECORD, $template->getResponsiblePermissions() & Template::PERMIT_VIEW_RECORD);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown template.
     */
    public function testNotFoundTemplate()
    {
        $command = new AddTemplatePermissionsCommand([
            'id'          => $this->getMaxId(),
            'group'       => SystemRole::REGISTERED,
            'permissions' => Template::PERMIT_VIEW_RECORD,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown group.
     */
    public function testNotFoundGroup()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);
        self::assertNotNull($template);

        $command = new RemoveTemplatePermissionsCommand([
            'id'          => $template->getId(),
            'group'       => $this->getMaxId(),
            'permissions' => Template::PERMIT_VIEW_RECORD,
        ]);

        $this->command_bus->handle($command);
    }
}
