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

use eTraxis\Collection\SystemRole;
use eTraxis\Entity\Template;
use eTraxis\Tests\BaseTestCase;

class AddRemoveTemplatePermissionsCommandTest extends BaseTestCase
{
    public function testExistingGroupPermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);
        $this->assertNotNull($template);

        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'Managers']);
        $this->assertNotNull($group);

        /** @var \eTraxis\Entity\TemplateGroupPermission $permissions */
        $permissions = $this->doctrine->getRepository('eTraxis:TemplateGroupPermission')->findOneBy([
            'groupId'    => $group->getId(),
            'templateId' => $template->getId(),
        ]);
        $this->assertNotNull($permissions);

        $this->assertEquals(Template::PERMIT_ADD_FILE,    $permissions->getPermission() & Template::PERMIT_ADD_FILE);
        $this->assertEquals(Template::PERMIT_REMOVE_FILE, $permissions->getPermission() & Template::PERMIT_REMOVE_FILE);
        $this->assertEquals(0,                            $permissions->getPermission() & Template::PERMIT_ATTACH_SUBRECORD);
        $this->assertEquals(0,                            $permissions->getPermission() & Template::PERMIT_DETACH_SUBRECORD);

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

        $permissions = $this->doctrine->getRepository('eTraxis:TemplateGroupPermission')->findOneBy([
            'groupId'    => $group->getId(),
            'templateId' => $template->getId(),
        ]);

        $this->assertEquals(0,                                 $permissions->getPermission() & Template::PERMIT_ADD_FILE);
        $this->assertEquals(0,                                 $permissions->getPermission() & Template::PERMIT_REMOVE_FILE);
        $this->assertEquals(Template::PERMIT_ATTACH_SUBRECORD, $permissions->getPermission() & Template::PERMIT_ATTACH_SUBRECORD);
        $this->assertEquals(Template::PERMIT_DETACH_SUBRECORD, $permissions->getPermission() & Template::PERMIT_DETACH_SUBRECORD);
    }

    public function testNewGroupPermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Futurama']);
        $this->assertNotNull($template);

        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'Managers']);
        $this->assertNotNull($group);

        /** @var \eTraxis\Entity\TemplateGroupPermission $permissions */
        $permissions = $this->doctrine->getRepository('eTraxis:TemplateGroupPermission')->findOneBy([
            'groupId'    => $group->getId(),
            'templateId' => $template->getId(),
        ]);
        $this->assertNull($permissions);

        $command = new AddTemplatePermissionsCommand([
            'id'          => $template->getId(),
            'group'       => $group->getId(),
            'permissions' => Template::PERMIT_VIEW_RECORD,
        ]);

        $this->command_bus->handle($command);

        $permissions = $this->doctrine->getRepository('eTraxis:TemplateGroupPermission')->findOneBy([
            'groupId'    => $group->getId(),
            'templateId' => $template->getId(),
        ]);
        $this->assertNotNull($permissions);

        $this->assertEquals(Template::PERMIT_VIEW_RECORD, $permissions->getPermission() & Template::PERMIT_VIEW_RECORD);
    }

    public function testRegisteredPermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);
        $this->assertNotNull($template);
        $id = $template->getId();

        $this->assertEquals(0, $template->getRegisteredPermissions() & Template::PERMIT_VIEW_RECORD);
        $this->assertEquals(0, $template->getRegisteredPermissions() & Template::PERMIT_CREATE_RECORD);

        $command = new AddTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => SystemRole::REGISTERED,
            'permissions' => Template::PERMIT_VIEW_RECORD | Template::PERMIT_CREATE_RECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository('eTraxis:Template')->find($id);

        $this->assertEquals(Template::PERMIT_VIEW_RECORD,   $template->getRegisteredPermissions() & Template::PERMIT_VIEW_RECORD);
        $this->assertEquals(Template::PERMIT_CREATE_RECORD, $template->getRegisteredPermissions() & Template::PERMIT_CREATE_RECORD);

        $command = new RemoveTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => SystemRole::REGISTERED,
            'permissions' => Template::PERMIT_CREATE_RECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository('eTraxis:Template')->find($id);

        $this->assertEquals(Template::PERMIT_VIEW_RECORD, $template->getRegisteredPermissions() & Template::PERMIT_VIEW_RECORD);
        $this->assertEquals(0,                            $template->getRegisteredPermissions() & Template::PERMIT_CREATE_RECORD);
    }

    public function testAuthorPermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);
        $this->assertNotNull($template);
        $id = $template->getId();

        $this->assertEquals(Template::PERMIT_EDIT_RECORD, $template->getAuthorPermissions() & Template::PERMIT_EDIT_RECORD);
        $this->assertEquals(0,                            $template->getAuthorPermissions() & Template::PERMIT_REOPEN_RECORD);

        $command = new AddTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => SystemRole::AUTHOR,
            'permissions' => Template::PERMIT_REOPEN_RECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository('eTraxis:Template')->find($id);

        $this->assertEquals(Template::PERMIT_REOPEN_RECORD, $template->getAuthorPermissions() & Template::PERMIT_REOPEN_RECORD);

        $command = new RemoveTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => SystemRole::AUTHOR,
            'permissions' => Template::PERMIT_EDIT_RECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository('eTraxis:Template')->find($id);

        $this->assertEquals(0, $template->getAuthorPermissions() & Template::PERMIT_EDIT_RECORD);

        $command = new RemoveTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => SystemRole::AUTHOR,
            'permissions' => Template::PERMIT_VIEW_RECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository('eTraxis:Template')->find($id);

        $this->assertEquals(Template::PERMIT_VIEW_RECORD, $template->getAuthorPermissions() & Template::PERMIT_VIEW_RECORD);
    }

    public function testResponsiblePermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);
        $this->assertNotNull($template);
        $id = $template->getId();

        $this->assertEquals(Template::PERMIT_ADD_COMMENT, $template->getResponsiblePermissions() & Template::PERMIT_ADD_COMMENT);
        $this->assertEquals(0,                            $template->getResponsiblePermissions() & Template::PERMIT_ATTACH_SUBRECORD);

        $command = new AddTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => SystemRole::RESPONSIBLE,
            'permissions' => Template::PERMIT_ATTACH_SUBRECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository('eTraxis:Template')->find($id);

        $this->assertEquals(Template::PERMIT_ATTACH_SUBRECORD, $template->getResponsiblePermissions() & Template::PERMIT_ATTACH_SUBRECORD);

        $command = new RemoveTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => SystemRole::RESPONSIBLE,
            'permissions' => Template::PERMIT_ADD_COMMENT,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository('eTraxis:Template')->find($id);

        $this->assertEquals(0, $template->getResponsiblePermissions() & Template::PERMIT_ADD_COMMENT);

        $command = new RemoveTemplatePermissionsCommand([
            'id'          => $id,
            'group'       => SystemRole::RESPONSIBLE,
            'permissions' => Template::PERMIT_VIEW_RECORD,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository('eTraxis:Template')->find($id);

        $this->assertEquals(Template::PERMIT_VIEW_RECORD, $template->getResponsiblePermissions() & Template::PERMIT_VIEW_RECORD);
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
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);
        $this->assertNotNull($template);

        $command = new RemoveTemplatePermissionsCommand([
            'id'          => $template->getId(),
            'group'       => $this->getMaxId(),
            'permissions' => Template::PERMIT_VIEW_RECORD,
        ]);

        $this->command_bus->handle($command);
    }
}
