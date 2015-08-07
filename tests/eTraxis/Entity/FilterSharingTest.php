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

class FilterSharingTest extends \PHPUnit_Framework_TestCase
{
    /** @var FilterSharing */
    private $object = null;

    protected function setUp()
    {
        $this->object = new FilterSharing();
    }

    public function testFilterId()
    {
        $this->assertNull($this->object->getFilterId());
    }

    public function testGroupId()
    {
        $this->assertNull($this->object->getGroupId());
    }

    public function testFilter()
    {
        $this->object->setFilter($filter = new Filter());
        $this->assertSame($filter, $this->object->getFilter());
    }

    public function testGroup()
    {
        $this->object->setGroup($group = new Group());
        $this->assertSame($group, $this->object->getGroup());
    }
}
