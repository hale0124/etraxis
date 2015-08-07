<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

class FilterStateTest extends \PHPUnit_Framework_TestCase
{
    /** @var FilterState */
    private $object = null;

    protected function setUp()
    {
        $this->object = new FilterState();
    }

    public function testFilterId()
    {
        $this->assertNull($this->object->getFilterId());
    }

    public function testStateId()
    {
        $this->assertNull($this->object->getStateId());
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
