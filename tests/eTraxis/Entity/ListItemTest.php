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

    public function testFieldValid()
    {
        $field = new Field();
        $field->setType(Field::TYPE_LIST);
        $this->object->setField($field);
        self::assertEquals($field, $this->object->getField());
    }

    public function testFieldInvalid()
    {
        $field = new Field();
        $field->setType(Field::TYPE_STRING);
        $this->object->setField($field);
        self::assertNotEquals($field, $this->object->getField());
    }

    public function testKey()
    {
        $expected = random_int(1, PHP_INT_MAX);
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
