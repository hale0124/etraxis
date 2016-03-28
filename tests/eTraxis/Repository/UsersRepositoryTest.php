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

use eTraxis\Entity\User;
use eTraxis\Tests\BaseTestCase;

class UsersRepositoryTest extends BaseTestCase
{
    public function testGetUserGroupsFound()
    {
        /** @var UsersRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(User::class);

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

        self::assertEquals($expected, $groups);
    }

    public function testGetUserGroupsNotFound()
    {
        /** @var UsersRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(User::class);

        $result = $repository->getUserGroups($this->getMaxId());

        $expected = [];

        self::assertEquals($expected, $result);
    }

    public function testGetOtherGroupsFound()
    {
        /** @var UsersRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(User::class);

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

        self::assertEquals($expected, $groups);
    }

    public function testGetOtherGroupsNotFound()
    {
        /** @var UsersRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(User::class);

        $result = $repository->getOtherGroups($this->getMaxId());

        $expected = [];

        self::assertEquals($expected, $result);
    }
}
