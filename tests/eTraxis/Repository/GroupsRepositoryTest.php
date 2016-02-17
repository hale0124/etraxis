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
use eTraxis\Tests\BaseTestCase;

class GroupsRepositoryTest extends BaseTestCase
{
    public function testGetGroups()
    {
        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);
        $this->assertNotNull($project);

        /** @var GroupsRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(Group::class);

        $result = $repository->getGroups($project->getId());

        $groups = array_map(function ($group) {
            return $group['name'];
        }, $result);

        $expected = [
            'Crew',
            'Managers',
            'Nimbus',
            'Planet Express, Inc.',
            'Staff',
        ];

        $this->assertEquals($expected, $groups);
    }

    public function testGetLocalGroups()
    {
        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);
        $this->assertNotNull($project);

        /** @var GroupsRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(Group::class);

        $result = $repository->getLocalGroups($project->getId());

        $groups = array_map(function ($group) {
            return $group['name'];
        }, $result);

        $expected = [
            'Crew',
            'Managers',
            'Staff',
        ];

        $this->assertEquals($expected, $groups);
    }

    public function testGetGlobalGroups()
    {
        /** @var GroupsRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(Group::class);

        $result = $repository->getGlobalGroups();

        $groups = array_map(function ($group) {
            return $group['name'];
        }, $result);

        $expected = [
            'Nimbus',
            'Planet Express, Inc.',
        ];

        $this->assertEquals($expected, $groups);
    }

    public function testGetGroupMembersFound()
    {
        /** @var GroupsRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(Group::class);

        /** @var Group $group */
        $group = $repository->findOneBy(['name' => 'Staff']);

        $result = $repository->getGroupMembers($group->getId());

        $users = array_map(function ($user) {
            /** @var \eTraxis\Entity\User $user */
            return $user->getUsername();
        }, $result);

        $expected = [
            'bender',
            'amy',
            'zoidberg',
            'fry',
            'scruffy',
            'leela',
        ];

        $this->assertEquals($expected, $users);
    }

    public function testGetGroupMembersNotFound()
    {
        /** @var GroupsRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(Group::class);

        $result = $repository->getGroupMembers($this->getMaxId());

        $expected = [];

        $this->assertEquals($expected, $result);
    }

    public function testGetGroupNonMembersFound()
    {
        /** @var GroupsRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(Group::class);

        /** @var Group $group */
        $group = $repository->findOneBy(['name' => 'Staff']);

        $result = $repository->getGroupNonMembers($group->getId());

        $users = array_map(function ($user) {
            /** @var \eTraxis\Entity\User $user */
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

        $this->assertEquals($expected, $users);
    }

    public function testGetGroupNonMembersNotFound()
    {
        /** @var GroupsRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(Group::class);

        $result = $repository->getGroupNonMembers($this->getMaxId());

        $expected = [];

        $this->assertEquals($expected, $result);
    }
}
