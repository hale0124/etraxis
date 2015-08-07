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

class FilterStatusTest extends \PHPUnit_Framework_TestCase
{
    /** @var FilterStatus */
    private $object = null;

    protected function setUp()
    {
        $this->object = new FilterStatus();
    }

    public function testFilterId()
    {
        $this->assertNull($this->object->getFilterId());
    }

    public function testUserId()
    {
        $this->assertNull($this->object->getUserId());
    }

    public function testFilter()
    {
        $this->object->setFilter($filter = new Filter());
        $this->assertSame($filter, $this->object->getFilter());
    }

    public function testUser()
    {
        $this->object->setUser($user = new User());
        $this->assertSame($user, $this->object->getUser());
    }
}
