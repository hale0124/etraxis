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

class FilterTransitionTest extends \PHPUnit_Framework_TestCase
{
    /** @var FilterTransition */
    private $object;

    protected function setUp()
    {
        $this->object = new FilterTransition();
    }

    public function testFilterId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setFilterId($expected);
        $this->assertEquals($expected, $this->object->getFilterId());
    }

    public function testStateId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setStateId($expected);
        $this->assertEquals($expected, $this->object->getStateId());
    }

    public function testDate1()
    {
        $expected = time();
        $this->object->setDate1($expected);
        $this->assertEquals($expected, $this->object->getDate1());
    }

    public function testDate2()
    {
        $expected = time();
        $this->object->setDate2($expected);
        $this->assertEquals($expected, $this->object->getDate2());
    }

    public function testFilter()
    {
        $this->object->setFilter($filter = new Filter());
        $this->assertSame($filter, $this->object->getFilter());
    }

    public function testState()
    {
        $this->object->setState($state = new State());
        $this->assertSame($state, $this->object->getState());
    }
}
