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

class UsersRepositoryTest extends BaseTestCase
{
    public function testGetUserGroupsFound()
    {
        /** @var UsersRepository $repository */
        $repository = $this->doctrine->getEntityManager()->getRepository('eTraxis:User');

        $user = $this->findUser('hubert');

        $result = $repository->getUserGroups($user->getId());

        $groups = array_map(function ($group) {
            /** @var \eTraxis\Entity\Group $group */
            return $group->getName();
        }, $result);

        $expected = [
            'Planet Express, Inc.',
            'Crew',
            'Managers',
        ];

        $this->assertEquals($expected, $groups);
    }

    public function testGetUserGroupsNotFound()
    {
        /** @var UsersRepository $repository */
        $repository = $this->doctrine->getEntityManager()->getRepository('eTraxis:User');

        $result = $repository->getUserGroups($this->getMaxId());

        $expected = [];

        $this->assertEquals($expected, $result);
    }

    public function testGetOtherGroupsFound()
    {
        /** @var UsersRepository $repository */
        $repository = $this->doctrine->getEntityManager()->getRepository('eTraxis:User');

        $user = $this->findUser('hubert');

        $result = $repository->getOtherGroups($user->getId());

        $groups = array_map(function ($group) {
            /** @var \eTraxis\Entity\Group $group */
            return $group->getName();
        }, $result);

        $expected = [
            'Nimbus',
            'Staff',
        ];

        $this->assertEquals($expected, $groups);
    }

    public function testGetOtherGroupsNotFound()
    {
        /** @var UsersRepository $repository */
        $repository = $this->doctrine->getEntityManager()->getRepository('eTraxis:User');

        $result = $repository->getOtherGroups($this->getMaxId());

        $expected = [];

        $this->assertEquals($expected, $result);
    }
}
