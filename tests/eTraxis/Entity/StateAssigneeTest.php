<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

class StateAssigneeTest extends \PHPUnit_Framework_TestCase
{
    /** @var StateAssignee */
    private $object;

    protected function setUp()
    {
        $this->object = new StateAssignee();
    }

    public function testState()
    {
        $this->object->setState($state = new State());
        self::assertSame($state, $this->object->getState());
    }

    public function testGroup()
    {
        $this->object->setGroup($group = new Group());
        self::assertSame($group, $this->object->getGroup());
    }
}
