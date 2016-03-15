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

class FilterFieldTest extends \PHPUnit_Framework_TestCase
{
    /** @var FilterField */
    private $object;

    protected function setUp()
    {
        $this->object = new FilterField();
    }

    public function testFilterId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setFilterId($expected);
        $this->assertEquals($expected, $this->object->getFilterId());
    }

    public function testFieldId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setFieldId($expected);
        $this->assertEquals($expected, $this->object->getFieldId());
    }

    public function testParameter1()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setParameter1($expected);
        $this->assertEquals($expected, $this->object->getParameter1());
    }

    public function testParameter2()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setParameter2($expected);
        $this->assertEquals($expected, $this->object->getParameter2());
    }

    public function testFilter()
    {
        $this->object->setFilter($filter = new Filter());
        $this->assertSame($filter, $this->object->getFilter());
    }

    public function testField()
    {
        $this->object->setField($field = new Field());
        $this->assertSame($field, $this->object->getField());
    }
}
