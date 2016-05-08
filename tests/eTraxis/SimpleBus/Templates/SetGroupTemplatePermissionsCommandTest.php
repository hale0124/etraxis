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

use eTraxis\Entity\Group;
use eTraxis\Entity\Template;
use eTraxis\Entity\TemplateGroupPermission;
use eTraxis\Tests\BaseTestCase;

class SetGroupTemplatePermissionsCommandTest extends BaseTestCase
{
    public function testExistingGroupPermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);

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

        $command = new SetGroupTemplatePermissionsCommand([
            'id'          => $template->getId(),
            'group'       => $group->getId(),
            'permissions' => Template::PERMIT_ATTACH_SUBRECORD | Template::PERMIT_DETACH_SUBRECORD,
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

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);

        /** @var TemplateGroupPermission $permissions */
        $permissions = $this->doctrine->getRepository(TemplateGroupPermission::class)->findOneBy([
            'group'    => $group,
            'template' => $template,
        ]);
        self::assertNull($permissions);

        $command = new SetGroupTemplatePermissionsCommand([
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

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown template.
     */
    public function testNotFoundTemplate()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);

        $command = new SetGroupTemplatePermissionsCommand([
            'id'          => self::UNKNOWN_ENTITY_ID,
            'group'       => $group->getId(),
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

        $command = new SetGroupTemplatePermissionsCommand([
            'id'          => $template->getId(),
            'group'       => self::UNKNOWN_ENTITY_ID,
            'permissions' => Template::PERMIT_VIEW_RECORD,
        ]);

        $this->command_bus->handle($command);
    }
}
