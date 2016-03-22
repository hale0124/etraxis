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

class DurationFieldTest extends BaseTestCase
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

        $this->assertCount(count($expected), $actual);

        foreach ($expected as $key) {
            $this->assertContains($key, $actual);
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
        $this->assertEquals($min, $field->getMinValue());
        $this->assertEquals(DurationField::MIN_VALUE, $this->object->getParameter1());

        $field->setMinValue($value);
        $this->assertEquals($value, $field->getMinValue());
        $this->assertEquals($duration, $this->object->getParameter1());

        $field->setMinValue($min);
        $this->assertEquals($min, $field->getMinValue());
        $this->assertEquals(DurationField::MIN_VALUE, $this->object->getParameter1());

        $field->setMinValue($max);
        $this->assertEquals($max, $field->getMinValue());
        $this->assertEquals(DurationField::MAX_VALUE, $this->object->getParameter1());
    }

    public function testMaxValue()
    {
        $field = $this->object->asDuration();

        $duration = 866;
        $value    = '14:26';
        $min      = '0:00';
        $max      = '999999:59';

        $field->setMaxValue(null);
        $this->assertEquals($max, $field->getMaxValue());
        $this->assertEquals(DurationField::MAX_VALUE, $this->object->getParameter2());

        $field->setMaxValue($value);
        $this->assertEquals($value, $field->getMaxValue());
        $this->assertEquals($duration, $this->object->getParameter2());

        $field->setMaxValue($min);
        $this->assertEquals($min, $field->getMaxValue());
        $this->assertEquals(DurationField::MIN_VALUE, $this->object->getParameter2());

        $field->setMaxValue($max);
        $this->assertEquals($max, $field->getMaxValue());
        $this->assertEquals(DurationField::MAX_VALUE, $this->object->getParameter2());
    }

    public function testDefaultValue()
    {
        $field = $this->object->asDuration();

        $duration = 866;
        $value    = '14:26';
        $min      = '0:00';
        $max      = '999999:59';

        $field->setDefaultValue($value);
        $this->assertEquals($value, $field->getDefaultValue());
        $this->assertEquals($duration, $this->object->getDefaultValue());

        $field->setDefaultValue($min);
        $this->assertEquals($min, $field->getDefaultValue());
        $this->assertEquals(DurationField::MIN_VALUE, $this->object->getDefaultValue());

        $field->setDefaultValue($max);
        $this->assertEquals($max, $field->getDefaultValue());
        $this->assertEquals(DurationField::MAX_VALUE, $this->object->getDefaultValue());

        $field->setDefaultValue(null);
        $this->assertNull($field->getDefaultValue());
        $this->assertNull($this->object->getDefaultValue());
    }

    public function testInvalidValues()
    {
        $field = $this->object->asDuration();

        $min = '0:00';
        $max = '999999:59';

        $this->object->setDefaultValue(-1);
        $this->assertEquals($min, $field->getDefaultValue());

        $this->object->setDefaultValue(60000000);
        $this->assertEquals($max, $field->getDefaultValue());

        $field->setDefaultValue('0:99');
        $this->assertNull($field->getDefaultValue());
    }
}
