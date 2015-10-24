<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Repository;

use eTraxis\Tests\BaseTestCase;

class GroupsRepositoryTest extends BaseTestCase
{
    public function testGetGroupMembersFound()
    {
        /** @var GroupsRepository $repository */
        $repository = $this->doctrine->getEntityManager()->getRepository('eTraxis:Group');

        /** @var \eTraxis\Entity\Group $group */
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
        $repository = $this->doctrine->getEntityManager()->getRepository('eTraxis:Group');

        $result = $repository->getGroupMembers($this->getMaxId());

        $expected = [];

        $this->assertEquals($expected, $result);
    }

    public function testGetGroupNonMembersFound()
    {
        /** @var GroupsRepository $repository */
        $repository = $this->doctrine->getEntityManager()->getRepository('eTraxis:Group');

        /** @var \eTraxis\Entity\Group $group */
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
        $repository = $this->doctrine->getEntityManager()->getRepository('eTraxis:Group');

        $result = $repository->getGroupNonMembers($this->getMaxId());

        $expected = [];

        $this->assertEquals($expected, $result);
    }
}
