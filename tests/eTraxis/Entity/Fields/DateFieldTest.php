<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Fields;

use eTraxis\Entity\Field;
use eTraxis\Tests\BaseTestCase;

class DateFieldTest extends BaseTestCase
{
    /** @var Field */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new Field();
        $this->object->setType(Field::TYPE_DATE);
    }

    public function testSupportedKeys()
    {
        $expected = ['minValue', 'maxValue', 'defaultValue'];

        $field = $this->object->asDate();

        $reflection = new \ReflectionObject($field);
        $method     = $reflection->getMethod('getSupportedKeys');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($field, []);

        $this->assertCount(count($expected), $actual);

        foreach ($expected as $key) {
            $this->assertContains($key, $actual);
        }
    }

    public function testMinValue()
    {
        $field = $this->object->asDate();

        $value = mt_rand(DateField::MIN_VALUE, DateField::MAX_VALUE);
        $min   = DateField::MIN_VALUE - 1;
        $max   = DateField::MAX_VALUE + 1;

        $field->setMinValue($value);
        $this->assertEquals($value, $field->getMinValue());
        $this->assertEquals($value, $this->object->getParameter1());

        $field->setMinValue($min);
        $this->assertEquals(DateField::MIN_VALUE, $field->getMinValue());

        $field->setMinValue($max);
        $this->assertEquals(DateField::MAX_VALUE, $field->getMinValue());
    }

    public function testMaxValue()
    {
        $field = $this->object->asDate();

        $value = mt_rand(DateField::MIN_VALUE, DateField::MAX_VALUE);
        $min   = DateField::MIN_VALUE - 1;
        $max   = DateField::MAX_VALUE + 1;

        $field->setMaxValue($value);
        $this->assertEquals($value, $field->getMaxValue());
        $this->assertEquals($value, $this->object->getParameter2());

        $field->setMaxValue($min);
        $this->assertEquals(DateField::MIN_VALUE, $field->getMaxValue());

        $field->setMaxValue($max);
        $this->assertEquals(DateField::MAX_VALUE, $field->getMaxValue());
    }

    public function testDefaultValue()
    {
        $field = $this->object->asDate();

        $value = mt_rand(DateField::MIN_VALUE, DateField::MAX_VALUE);
        $min   = DateField::MIN_VALUE - 1;
        $max   = DateField::MAX_VALUE + 1;

        $field->setDefaultValue($value);
        $this->assertEquals($value, $field->getDefaultValue());
        $this->assertEquals($value, $this->object->getDefaultValue());

        $field->setDefaultValue($min);
        $this->assertEquals(DateField::MIN_VALUE, $field->getDefaultValue());

        $field->setDefaultValue($max);
        $this->assertEquals(DateField::MAX_VALUE, $field->getDefaultValue());

        $field->setDefaultValue(null);
        $this->assertNull($field->getDefaultValue());
        $this->assertNull($this->object->getDefaultValue());
    }
}
