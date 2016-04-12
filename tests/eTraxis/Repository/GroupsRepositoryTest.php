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

use eTraxis\Entity\Group;
use eTraxis\Entity\Project;
use eTraxis\Entity\User;
use eTraxis\Tests\BaseTestCase;

class GroupsRepositoryTest extends BaseTestCase
{
    public function testGetGroups()
    {
        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);
        self::assertNotNull($project);

        /** @var GroupsRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(Group::class);

        $result = $repository->getGroups($project);

        $groups = array_map(function (Group $group) {
            return $group->getName();
        }, $result);

        $expected = [
            'Crew',
            'Managers',
            'Nimbus',
            'Planet Express, Inc.',
            'Staff',
        ];

        self::assertEquals($expected, $groups);
    }

    public function testGetGlobalGroups()
    {
        /** @var GroupsRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(Group::class);

        $result = $repository->getGlobalGroups();

        $groups = array_map(function (Group $group) {
            return $group->getName();
        }, $result);

        $expected = [
            'Nimbus',
            'Planet Express, Inc.',
        ];

        self::assertEquals($expected, $groups);
    }

    public function testGetGroupNonMembers()
    {
        /** @var GroupsRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(Group::class);

        /** @var Group $group */
        $group = $repository->findOneBy(['name' => 'Staff']);

        $result = $repository->getGroupNonMembers($group);

        $users = array_map(function (User $user) {
            return $user->getUsername();
        }, $result);

        $expected = [
            'einstein',
            'artem',
            'veins',
            'francine',
            'hermes',
            'hubert',
            'kif',
            'zapp',
        ];

        self::assertEquals($expected, $users);
    }
}
