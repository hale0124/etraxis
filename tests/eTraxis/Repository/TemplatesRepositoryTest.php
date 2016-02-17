<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Repository;

use eTraxis\Collection\SystemRole;
use eTraxis\Entity\Group;
use eTraxis\Entity\Project;
use eTraxis\Entity\Template;
use eTraxis\Tests\BaseTestCase;

class TemplatesRepositoryTest extends BaseTestCase
{
    public function testGetTemplates()
    {
        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);
        $this->assertNotNull($project);

        /** @var TemplatesRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(Template::class);

        $result = $repository->getTemplates($project->getId());

        $templates = array_map(function ($template) {
            return $template['name'];
        }, $result);

        $expected = [
            'Delivery',
            'Futurama',
        ];

        $this->assertEquals($expected, $templates);
    }

    public function testGetRolePermissions()
    {
        $author      = Template::PERMIT_VIEW_RECORD | Template::PERMIT_EDIT_RECORD | Template::PERMIT_ADD_COMMENT | Template::PERMIT_ADD_FILE | Template::PERMIT_REMOVE_FILE;
        $responsible = Template::PERMIT_VIEW_RECORD | Template::PERMIT_ADD_COMMENT | Template::PERMIT_ADD_FILE;
        $registered  = 0;

        /** @var TemplatesRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(Template::class);

        /** @var Template $template */
        $template = $repository->findOneBy(['name' => 'Delivery']);

        $this->assertEquals($author,      $repository->getRolePermissions($template->getId(), SystemRole::AUTHOR));
        $this->assertEquals($responsible, $repository->getRolePermissions($template->getId(), SystemRole::RESPONSIBLE));
        $this->assertEquals($registered,  $repository->getRolePermissions($template->getId(), SystemRole::REGISTERED));
        $this->assertEquals(0,            $repository->getRolePermissions($template->getId(), 0));
    }

    public function testGetGroupPermissions()
    {
        $local  = Template::PERMIT_VIEW_RECORD | Template::PERMIT_ADD_COMMENT;
        $global = 0;

        /** @var Group $group_local */
        /** @var Group $group_global */
        $group_local  = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Crew']);
        $group_global = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Nimbus']);
        $this->assertNotNull($group_local);
        $this->assertNotNull($group_global);

        /** @var TemplatesRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(Template::class);

        /** @var Template $template */
        $template = $repository->findOneBy(['name' => 'Delivery']);

        $this->assertEquals($local,  $repository->getGroupPermissions($template->getId(), $group_local->getId()));
        $this->assertEquals($global, $repository->getGroupPermissions($template->getId(), $group_global->getId()));
    }
}
