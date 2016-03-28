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

class FilterStatusTest extends \PHPUnit_Framework_TestCase
{
    /** @var FilterStatus */
    private $object;

    protected function setUp()
    {
        $this->object = new FilterStatus();
    }

    public function testFilterId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setFilterId($expected);
        self::assertEquals($expected, $this->object->getFilterId());
    }

    public function testUserId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setUserId($expected);
        self::assertEquals($expected, $this->object->getUserId());
    }

    public function testFilter()
    {
        $this->object->setFilter($filter = new Filter());
        self::assertSame($filter, $this->object->getFilter());
    }

    public function testUser()
    {
        $this->object->setUser($user = new User());
        self::assertSame($user, $this->object->getUser());
    }
}
