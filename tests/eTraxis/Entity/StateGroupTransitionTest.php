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

class StateGroupTransitionTest extends \PHPUnit_Framework_TestCase
{
    /** @var StateGroupTransition */
    private $object;

    protected function setUp()
    {
        $this->object = new StateGroupTransition();
    }

    public function testFromState()
    {
        $this->object->setFromState($state = new State());
        self::assertEquals($state, $this->object->getFromState());
    }

    public function testToState()
    {
        $this->object->setToState($state = new State());
        self::assertEquals($state, $this->object->getToState());
    }

    public function testGroup()
    {
        $this->object->setGroup($group = new Group());
        self::assertEquals($group, $this->object->getGroup());
    }
}
