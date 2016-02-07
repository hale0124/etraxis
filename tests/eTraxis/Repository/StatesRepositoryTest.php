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
use eTraxis\Tests\BaseTestCase;

class StatesRepositoryTest extends BaseTestCase
{
    public function testGetStates()
    {
        /** @var \eTraxis\Entity\Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);
        $this->assertNotNull($template);

        /** @var StatesRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository('eTraxis:State');

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
        $repository = $this->doctrine->getManager()->getRepository('eTraxis:State');

        /** @var \eTraxis\Entity\State $new */
        $new = $repository->findOneBy(['name' => 'New']);
        $this->assertNotNull($new);

        /** @var \eTraxis\Entity\State $delivered */
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
        $repository = $this->doctrine->getManager()->getRepository('eTraxis:State');

        /** @var \eTraxis\Entity\Group $managers */
        $managers = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'Managers']);
        $this->assertNotNull($managers);

        /** @var \eTraxis\Entity\State $new */
        $new = $repository->findOneBy(['name' => 'New']);
        $this->assertNotNull($new);

        /** @var \eTraxis\Entity\State $delivered */
        $delivered = $repository->findOneBy(['name' => 'Delivered']);
        $this->assertNotNull($delivered);

        $expected = [
            $delivered->getId(),
        ];

        $this->assertEquals($expected, $repository->getGroupTransitions($new->getId(), $managers->getId()));
    }
}
