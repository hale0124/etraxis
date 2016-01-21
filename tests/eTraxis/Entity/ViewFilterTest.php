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

class ViewFilterTest extends \PHPUnit_Framework_TestCase
{
    /** @var ViewFilter */
    private $object = null;

    protected function setUp()
    {
        $this->object = new ViewFilter();
    }

    public function testViewId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setViewId($expected);
        $this->assertEquals($expected, $this->object->getViewId());
    }

    public function testFilterId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setFilterId($expected);
        $this->assertEquals($expected, $this->object->getFilterId());
    }

    public function testView()
    {
        $this->object->setView($user = new View());
        $this->assertSame($user, $this->object->getView());
    }

    public function testFilter()
    {
        $this->object->setFilter($filter = new Filter());
        $this->assertSame($filter, $this->object->getFilter());
    }
}
