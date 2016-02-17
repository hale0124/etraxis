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
use eTraxis\Entity\Template;
use eTraxis\Tests\BaseTestCase;

class StatesRepositoryTest extends BaseTestCase
{
    public function testGetStates()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);
        $this->assertNotNull($template);

        /** @var StatesRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(State::class);

        $result = $repository->getStates($template->getId());

        $states = array_map(function ($state) {
            return $state['name'];
        }, $result);

        $expected = [
            'New',
            'Delivered',
        ];

        $this->assertEquals($expected, $states);
    }

    public function testGetRoleTransitions()
    {
        /** @var StatesRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(State::class);

        /** @var State $new */
        $new = $repository->findOneBy(['name' => 'New']);
        $this->assertNotNull($new);

        /** @var State $delivered */
        $delivered = $repository->findOneBy(['name' => 'Delivered']);
        $this->assertNotNull($delivered);

        $expected = [
            $delivered->getId(),
        ];

        $this->assertEquals($expected, $repository->getRoleTransitions($new->getId(), SystemRole::RESPONSIBLE));
    }

    public function testGetGroupTransitions()
    {
        /** @var StatesRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(State::class);

        /** @var Group $managers */
        $managers = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);
        $this->assertNotNull($managers);

        /** @var State $new */
        $new = $repository->findOneBy(['name' => 'New']);
        $this->assertNotNull($new);

        /** @var State $delivered */
        $delivered = $repository->findOneBy(['name' => 'Delivered']);
        $this->assertNotNull($delivered);

        $expected = [
            $delivered->getId(),
        ];

        $this->assertEquals($expected, $repository->getGroupTransitions($new->getId(), $managers->getId()));
    }
}
