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

use eTraxis\Entity\Template;
use eTraxis\Tests\BaseTestCase;

class TemplatesRepositoryTest extends BaseTestCase
{
    public function testGetTemplates()
    {
        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);
        $this->assertNotNull($project);

        /** @var TemplatesRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository('eTraxis:Template');

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

    public function testGetPermissions()
    {
        $local  = Template::PERMIT_VIEW_RECORD | Template::PERMIT_ADD_COMMENT;
        $global = 0;

        /** @var \eTraxis\Entity\Group $group_local */
        /** @var \eTraxis\Entity\Group $group_global */
        $group_local  = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'Crew']);
        $group_global = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'Nimbus']);
        $this->assertNotNull($group_local);
        $this->assertNotNull($group_global);

        /** @var TemplatesRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository('eTraxis:Template');

        /** @var Template $template */
        $template = $repository->findOneBy(['name' => 'Delivery']);

        $this->assertEquals($local,  $repository->getPermissions($template->getId(), $group_local->getId()));
        $this->assertEquals($global, $repository->getPermissions($template->getId(), $group_global->getId()));
    }
}
