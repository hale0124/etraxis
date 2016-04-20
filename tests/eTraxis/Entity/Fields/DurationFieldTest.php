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

class DurationFieldTest extends \PHPUnit_Framework_TestCase
{
    /** @var Field */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new Field();
        $this->object->setType(Field::TYPE_DURATION);
    }

    public function testSupportedKeys()
    {
        $expected = ['minValue', 'maxValue', 'defaultValue'];

        $field = $this->object->asDuration();

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
        $field = $this->object->asDuration();

        $duration = 866;
        $value    = '14:26';
        $min      = '0:00';
        $max      = '999999:59';

        $field->setMinValue(null);
        self::assertEquals($min, $field->getMinValue());
        self::assertEquals(DurationField::MIN_VALUE, $this->object->getParameters()->getParameter1());

        $field->setMinValue($value);
        self::assertEquals($value, $field->getMinValue());
        self::assertEquals($duration, $this->object->getParameters()->getParameter1());

        $field->setMinValue($min);
        self::assertEquals($min, $field->getMinValue());
        self::assertEquals(DurationField::MIN_VALUE, $this->object->getParameters()->getParameter1());

        $field->setMinValue($max);
        self::assertEquals($max, $field->getMinValue());
        self::assertEquals(DurationField::MAX_VALUE, $this->object->getParameters()->getParameter1());
    }

    public function testMaxValue()
    {
        $field = $this->object->asDuration();

        $duration = 866;
        $value    = '14:26';
        $min      = '0:00';
        $max      = '999999:59';

        $field->setMaxValue(null);
        self::assertEquals($max, $field->getMaxValue());
        self::assertEquals(DurationField::MAX_VALUE, $this->object->getParameters()->getParameter2());

        $field->setMaxValue($value);
        self::assertEquals($value, $field->getMaxValue());
        self::assertEquals($duration, $this->object->getParameters()->getParameter2());

        $field->setMaxValue($min);
        self::assertEquals($min, $field->getMaxValue());
        self::assertEquals(DurationField::MIN_VALUE, $this->object->getParameters()->getParameter2());

        $field->setMaxValue($max);
        self::assertEquals($max, $field->getMaxValue());
        self::assertEquals(DurationField::MAX_VALUE, $this->object->getParameters()->getParameter2());
    }

    public function testDefaultValue()
    {
        $field = $this->object->asDuration();

        $duration = 866;
        $value    = '14:26';
        $min      = '0:00';
        $max      = '999999:59';

        $field->setDefaultValue($value);
        self::assertEquals($value, $field->getDefaultValue());
        self::assertEquals($duration, $this->object->getParameters()->getDefaultValue());

        $field->setDefaultValue($min);
        self::assertEquals($min, $field->getDefaultValue());
        self::assertEquals(DurationField::MIN_VALUE, $this->object->getParameters()->getDefaultValue());

        $field->setDefaultValue($max);
        self::assertEquals($max, $field->getDefaultValue());
        self::assertEquals(DurationField::MAX_VALUE, $this->object->getParameters()->getDefaultValue());

        $field->setDefaultValue(null);
        self::assertNull($field->getDefaultValue());
        self::assertNull($this->object->getParameters()->getDefaultValue());
    }

    public function testInvalidValues()
    {
        $field = $this->object->asDuration();

        $min = '0:00';
        $max = '999999:59';

        $this->object->getParameters()->setDefaultValue(-1);
        self::assertEquals($min, $field->getDefaultValue());

        $this->object->getParameters()->setDefaultValue(60000000);
        self::assertEquals($max, $field->getDefaultValue());

        $field->setDefaultValue('0:99');
        self::assertNull($field->getDefaultValue());
    }
}
