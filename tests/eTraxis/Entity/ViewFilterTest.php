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
        $this->assertNull($this->object->getViewId());
    }

    public function testFilterId()
    {
        $this->assertNull($this->object->getFilterId());
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
