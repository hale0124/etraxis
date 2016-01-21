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

class FilterRoleTest extends \PHPUnit_Framework_TestCase
{
    /** @var FilterRole */
    private $object = null;

    protected function setUp()
    {
        $this->object = new FilterRole();
    }

    public function testFilterId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setFilterId($expected);
        $this->assertEquals($expected, $this->object->getFilterId());
    }

    public function testFlag()
    {
        $expected = FilterRole::AUTHOR;
        $this->object->setFlag($expected);
        $this->assertEquals($expected, $this->object->getFlag());
    }

    public function testUserId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setUserId($expected);
        $this->assertEquals($expected, $this->object->getUserId());
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
