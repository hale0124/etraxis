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

use eTraxis\Dictionary\FieldType;
use eTraxis\Dictionary\StateType;
use eTraxis\Entity\Field;
use eTraxis\Entity\Project;
use eTraxis\Entity\State;
use eTraxis\Entity\Template;

class NumberFieldTest extends \PHPUnit_Framework_TestCase
{
    /** @var Field */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $state = new State(new Template(new Project()), StateType::IS_INTERIM);

        $this->object = new Field($state, FieldType::NUMBER);
    }

    public function testSupportedKeys()
    {
        $expected = ['minValue', 'maxValue', 'defaultValue'];

        $field = $this->object->asNumber();

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
        $field = $this->object->asNumber();

        $value = random_int(NumberField::MIN_VALUE, NumberField::MAX_VALUE);
        $min   = -2000000000;
        $max   = 2000000000;

        $field->setMinValue($value);
        self::assertEquals($value, $field->getMinValue());
        self::assertEquals($value, $this->object->getParameters()->getParameter1());

        $field->setMinValue($min);
        self::assertEquals(NumberField::MIN_VALUE, $field->getMinValue());

        $field->setMinValue($max);
        self::assertEquals(NumberField::MAX_VALUE, $field->getMinValue());
    }

    public function testMaxValue()
    {
        $field = $this->object->asNumber();

        $value = random_int(NumberField::MIN_VALUE, NumberField::MAX_VALUE);
        $min   = -2000000000;
        $max   = 2000000000;

        $field->setMaxValue($value);
        self::assertEquals($value, $field->getMaxValue());
        self::assertEquals($value, $this->object->getParameters()->getParameter2());

        $field->setMaxValue($min);
        self::assertEquals(NumberField::MIN_VALUE, $field->getMaxValue());

        $field->setMaxValue($max);
        self::assertEquals(NumberField::MAX_VALUE, $field->getMaxValue());
    }

    public function testDefaultValue()
    {
        $field = $this->object->asNumber();

        $value = random_int(NumberField::MIN_VALUE, NumberField::MAX_VALUE);
        $min   = -2000000000;
        $max   = 2000000000;

        $field->setDefaultValue($value);
        self::assertEquals($value, $field->getDefaultValue());
        self::assertEquals($value, $this->object->getParameters()->getDefaultValue());

        $field->setDefaultValue($min);
        self::assertEquals(NumberField::MIN_VALUE, $field->getDefaultValue());

        $field->setDefaultValue($max);
        self::assertEquals(NumberField::MAX_VALUE, $field->getDefaultValue());

        $field->setDefaultValue(null);
        self::assertNull($field->getDefaultValue());
        self::assertNull($this->object->getParameters()->getDefaultValue());
    }
}
