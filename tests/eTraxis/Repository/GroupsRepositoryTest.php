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
use eTraxis\Tests\TransactionalTestCase;

class GroupsRepositoryTest extends TransactionalTestCase
{
    public function testGetGlobalGroups()
    {
        /** @var GroupsRepository $repository */
        $repository = $this->doctrine->getRepository(Group::class);

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
}
