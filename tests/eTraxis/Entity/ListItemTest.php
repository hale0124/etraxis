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

class ListItemTest extends \PHPUnit_Framework_TestCase
{
    /** @var ListItem */
    private $object;

    protected function setUp()
    {
        $this->object = new ListItem();
    }

    public function testField()
    {
        $this->object->setField($field = new Field());
        self::assertSame($field, $this->object->getField());
    }

    public function testKey()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setKey($expected);
        self::assertEquals($expected, $this->object->getKey());
    }

    public function testValue()
    {
        $expected = str_pad('_', 50, '_');
        $this->object->setValue($expected);
        self::assertEquals($expected, $this->object->getValue());
    }
}
