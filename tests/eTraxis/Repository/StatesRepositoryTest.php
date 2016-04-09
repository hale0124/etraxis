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
use eTraxis\Entity\State;
use eTraxis\Tests\BaseTestCase;

class StatesRepositoryTest extends BaseTestCase
{
    public function testGetRoleTransitions()
    {
        /** @var StatesRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(State::class);

        /** @var State $new */
        $new = $repository->findOneBy(['name' => 'New']);
        self::assertNotNull($new);

        /** @var State $delivered */
        $delivered = $repository->findOneBy(['name' => 'Delivered']);
        self::assertNotNull($delivered);

        $expected = [
            $delivered,
        ];

        self::assertEquals($expected, $repository->getRoleTransitions($new, SystemRole::RESPONSIBLE));
    }

    public function testGetGroupTransitions()
    {
        /** @var StatesRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(State::class);

        /** @var Group $managers */
        $managers = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);
        self::assertNotNull($managers);

        /** @var State $new */
        $new = $repository->findOneBy(['name' => 'New']);
        self::assertNotNull($new);

        /** @var State $delivered */
        $delivered = $repository->findOneBy(['name' => 'Delivered']);
        self::assertNotNull($delivered);

        $expected = [
            $delivered,
        ];

        self::assertEquals($expected, $repository->getGroupTransitions($new, $managers));
    }
}
