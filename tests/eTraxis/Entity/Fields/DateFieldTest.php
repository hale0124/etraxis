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

        self::assertCount(count($expected), $actual);

        foreach ($expected as $key) {
            self::assertContains($key, $actual);
        }
    }

    public function testMinValue()
    {
        $field = $this->object->asDate();

        $value = mt_rand(DateField::MIN_VALUE, DateField::MAX_VALUE);
        $min   = DateField::MIN_VALUE - 1;
        $max   = DateField::MAX_VALUE + 1;

        $field->setMinValue($value);
        self::assertEquals($value, $field->getMinValue());
        self::assertEquals($value, $this->object->getParameters()->getParameter1());

        $field->setMinValue($min);
        self::assertEquals(DateField::MIN_VALUE, $field->getMinValue());

        $field->setMinValue($max);
        self::assertEquals(DateField::MAX_VALUE, $field->getMinValue());
    }

    public function testMaxValue()
    {
        $field = $this->object->asDate();

        $value = mt_rand(DateField::MIN_VALUE, DateField::MAX_VALUE);
        $min   = DateField::MIN_VALUE - 1;
        $max   = DateField::MAX_VALUE + 1;

        $field->setMaxValue($value);
        self::assertEquals($value, $field->getMaxValue());
        self::assertEquals($value, $this->object->getParameters()->getParameter2());

        $field->setMaxValue($min);
        self::assertEquals(DateField::MIN_VALUE, $field->getMaxValue());

        $field->setMaxValue($max);
        self::assertEquals(DateField::MAX_VALUE, $field->getMaxValue());
    }

    public function testDefaultValue()
    {
        $field = $this->object->asDate();

        $value = mt_rand(DateField::MIN_VALUE, DateField::MAX_VALUE);
        $min   = DateField::MIN_VALUE - 1;
        $max   = DateField::MAX_VALUE + 1;

        $field->setDefaultValue($value);
        self::assertEquals($value, $field->getDefaultValue());
        self::assertEquals($value, $this->object->getParameters()->getDefaultValue());

        $field->setDefaultValue($min);
        self::assertEquals(DateField::MIN_VALUE, $field->getDefaultValue());

        $field->setDefaultValue($max);
        self::assertEquals(DateField::MAX_VALUE, $field->getDefaultValue());

        $field->setDefaultValue(null);
        self::assertNull($field->getDefaultValue());
        self::assertNull($this->object->getParameters()->getDefaultValue());
    }
}
