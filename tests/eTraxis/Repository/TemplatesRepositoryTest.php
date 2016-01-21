<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Repository;

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
}
