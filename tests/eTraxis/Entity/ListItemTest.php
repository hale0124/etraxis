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

use eTraxis\Dictionary\FieldType;

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
        $field->setType(FieldType::LIST);
        $this->object->setField($field);
        self::assertEquals($field, $this->object->getField());
    }

    public function testFieldInvalid()
    {
        $field = new Field();
        $field->setType(FieldType::STRING);
        $this->object->setField($field);
        self::assertNotEquals($field, $this->object->getField());
    }

    public function testValue()
    {
        $expected = random_int(1, PHP_INT_MAX);
        $this->object->setValue($expected);
        self::assertEquals($expected, $this->object->getValue());
    }

    public function testText()
    {
        $expected = str_pad('_', 50, '_');
        $this->object->setText($expected);
        self::assertEquals($expected, $this->object->getText());
    }
}
