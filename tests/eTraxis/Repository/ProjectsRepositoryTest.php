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

use eTraxis\Entity\Project;
use eTraxis\Tests\TransactionalTestCase;

class ProjectsRepositoryTest extends TransactionalTestCase
{
    public function testGetProjects()
    {
        /** @var ProjectsRepository $repository */
        $repository = $this->doctrine->getRepository(Project::class);

        $result = $repository->getProjects();

        $projects = array_map(function (Project $project) {
            return $project->getName();
        }, $result);

        $expected = [
            'eTraxis 1.0',
            'eTraxis 2.0',
            'eTraxis 3.0',
            'Planet Express',
        ];

        self::assertEquals($expected, $projects);
    }
}
