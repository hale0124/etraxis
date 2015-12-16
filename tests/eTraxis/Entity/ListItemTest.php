<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

class ListItemTest extends \PHPUnit_Framework_TestCase
{
    /** @var ListItem */
    private $object = null;

    protected function setUp()
    {
        $this->object = new ListItem();
    }

    public function testFieldId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setFieldId($expected);
        $this->assertEquals($expected, $this->object->getFieldId());
    }

    public function testKey()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setKey($expected);
        $this->assertEquals($expected, $this->object->getKey());
    }

    public function testValue()
    {
        $expected = str_pad('_', 50, '_');
        $this->object->setValue($expected);
        $this->assertEquals($expected, $this->object->getValue());
    }

    public function testField()
    {
        $this->object->setField($field = new Field());
        $this->assertSame($field, $this->object->getField());
    }
}
