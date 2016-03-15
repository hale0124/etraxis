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

class NumberFieldTest extends BaseTestCase
{
    /** @var Field */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new Field();
        $this->object->setType(Field::TYPE_NUMBER);
    }

    public function testMinValue()
    {
        $field = $this->object->asNumber();

        $value = mt_rand(NumberField::MIN_VALUE, NumberField::MAX_VALUE);
        $min   = -2000000000;
        $max   = 2000000000;

        $field->setMinValue($value);
        $this->assertEquals($value, $field->getMinValue());
        $this->assertEquals($value, $this->object->getParameter1());

        $field->setMinValue($min);
        $this->assertEquals(NumberField::MIN_VALUE, $field->getMinValue());

        $field->setMinValue($max);
        $this->assertEquals(NumberField::MAX_VALUE, $field->getMinValue());
    }

    public function testMaxValue()
    {
        $field = $this->object->asNumber();

        $value = mt_rand(NumberField::MIN_VALUE, NumberField::MAX_VALUE);
        $min   = -2000000000;
        $max   = 2000000000;

        $field->setMaxValue($value);
        $this->assertEquals($value, $field->getMaxValue());
        $this->assertEquals($value, $this->object->getParameter2());

        $field->setMaxValue($min);
        $this->assertEquals(NumberField::MIN_VALUE, $field->getMaxValue());

        $field->setMaxValue($max);
        $this->assertEquals(NumberField::MAX_VALUE, $field->getMaxValue());
    }

    public function testDefaultValue()
    {
        $field = $this->object->asNumber();

        $value = mt_rand(NumberField::MIN_VALUE, NumberField::MAX_VALUE);
        $min   = -2000000000;
        $max   = 2000000000;

        $field->setDefaultValue($value);
        $this->assertEquals($value, $field->getDefaultValue());
        $this->assertEquals($value, $this->object->getDefaultValue());

        $field->setDefaultValue($min);
        $this->assertEquals(NumberField::MIN_VALUE, $field->getDefaultValue());

        $field->setDefaultValue($max);
        $this->assertEquals(NumberField::MAX_VALUE, $field->getDefaultValue());

        $field->setDefaultValue(null);
        $this->assertNull($field->getDefaultValue());
        $this->assertNull($this->object->getDefaultValue());
    }
}
