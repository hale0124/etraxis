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

class StateResponsibleGroupTest extends \PHPUnit_Framework_TestCase
{
    /** @var StateResponsibleGroup */
    private $object;

    protected function setUp()
    {
        $this->object = new StateResponsibleGroup();
    }

    public function testState()
    {
        $this->object->setState($state = new State());
        self::assertEquals($state, $this->object->getState());
    }

    public function testGroup()
    {
        $this->object->setGroup($group = new Group());
        self::assertEquals($group, $this->object->getGroup());
    }
}
