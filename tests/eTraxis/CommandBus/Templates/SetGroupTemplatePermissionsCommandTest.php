<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Templates;

use eTraxis\Dictionary\TemplatePermission;
use eTraxis\Entity\Group;
use eTraxis\Entity\Template;
use eTraxis\Tests\TransactionalTestCase;

class SetGroupTemplatePermissionsCommandTest extends TransactionalTestCase
{
    public function testExistingGroupPermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);

        self::assertTrue(in_array(TemplatePermission::ATTACH_FILES, $template->getGroupPermissions($group)));
        self::assertTrue(in_array(TemplatePermission::DELETE_FILES, $template->getGroupPermissions($group)));
        self::assertFalse(in_array(TemplatePermission::ATTACH_SUBRECORDS, $template->getGroupPermissions($group)));
        self::assertFalse(in_array(TemplatePermission::DETACH_SUBRECORDS, $template->getGroupPermissions($group)));

        $command = new SetGroupTemplatePermissionsCommand([
            'id'          => $template->getId(),
            'group'       => $group->getId(),
            'permissions' => [
                TemplatePermission::ATTACH_FILES,
                TemplatePermission::ATTACH_SUBRECORDS,
                TemplatePermission::DETACH_SUBRECORDS,
            ],
        ]);

        $this->commandbus->handle($command);

        self::assertTrue(in_array(TemplatePermission::ATTACH_FILES, $template->getGroupPermissions($group)));
        self::assertFalse(in_array(TemplatePermission::DELETE_FILES, $template->getGroupPermissions($group)));
        self::assertTrue(in_array(TemplatePermission::ATTACH_SUBRECORDS, $template->getGroupPermissions($group)));
        self::assertTrue(in_array(TemplatePermission::DETACH_SUBRECORDS, $template->getGroupPermissions($group)));
    }

    public function testNewGroupPermissions()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Futurama']);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);

        self::assertEmpty($template->getGroupPermissions($group));

        $command = new SetGroupTemplatePermissionsCommand([
            'id'          => $template->getId(),
            'group'       => $group->getId(),
            'permissions' => [TemplatePermission::VIEW_RECORDS],
        ]);

        $this->commandbus->handle($command);

        $this->doctrine->getManager()->refresh($template);

        self::assertEquals([TemplatePermission::VIEW_RECORDS], $template->getGroupPermissions($group));
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
            'permissions' => [TemplatePermission::VIEW_RECORDS],
        ]);

        $this->commandbus->handle($command);
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
            'permissions' => [TemplatePermission::VIEW_RECORDS],
        ]);

        $this->commandbus->handle($command);
    }
}
