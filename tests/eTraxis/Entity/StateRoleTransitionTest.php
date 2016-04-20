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

use eTraxis\Dictionary\SystemRole;

class StateRoleTransitionTest extends \PHPUnit_Framework_TestCase
{
    /** @var StateRoleTransition */
    private $object;

    protected function setUp()
    {
        $this->object = new StateRoleTransition();
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

    public function testRole()
    {
        $expected = SystemRole::AUTHOR;
        $this->object->setRole($expected);
        self::assertEquals($expected, $this->object->getRole());
    }
}
