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

use eTraxis\Entity\DecimalValue;
use eTraxis\Entity\Field;
use eTraxis\Tests\BaseTestCase;

class DecimalFieldTest extends BaseTestCase
{
    /** @var Field */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new Field();

        /** @noinspection PhpParamsInspection */
        $this->object
            ->setType(Field::TYPE_DECIMAL)
            ->setDecimalValuesRepository($this->doctrine->getRepository(DecimalValue::class))
        ;
    }

    public function testSupportedKeys()
    {
        $expected = ['minValue', 'maxValue', 'defaultValue'];

        $field = $this->object->asDecimal();

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
        $field = $this->object->asDecimal();

        $pi  = '3.1415926535';
        $min = '-10000000000';
        $max = '10000000000';

        /** @var \eTraxis\Repository\DecimalValuesRepository $repository */
        $repository = $this->doctrine->getRepository(DecimalValue::class);

        /** @var DecimalValue $value */
        $value = $repository->findOneBy(['value' => $pi]);

        $field->setMinValue($pi);
        $this->assertEquals($pi, $field->getMinValue());
        $this->assertEquals($value->getId(), $this->object->getParameter1());

        $field->setMinValue($min);
        $this->assertEquals(DecimalField::MIN_VALUE, $field->getMinValue());

        $field->setMinValue($max);
        $this->assertEquals(DecimalField::MAX_VALUE, $field->getMinValue());
    }

    public function testMaxValue()
    {
        $field = $this->object->asDecimal();

        $pi  = '3.1415926535';
        $min = '-10000000000';
        $max = '10000000000';

        /** @var \eTraxis\Repository\DecimalValuesRepository $repository */
        $repository = $this->doctrine->getRepository(DecimalValue::class);

        /** @var DecimalValue $value */
        $value = $repository->findOneBy(['value' => $pi]);

        $field->setMaxValue($pi);
        $this->assertEquals($pi, $field->getMaxValue());
        $this->assertEquals($value->getId(), $this->object->getParameter2());

        $field->setMaxValue($min);
        $this->assertEquals(DecimalField::MIN_VALUE, $field->getMaxValue());

        $field->setMaxValue($max);
        $this->assertEquals(DecimalField::MAX_VALUE, $field->getMaxValue());
    }

    public function testDefaultValue()
    {
        $field = $this->object->asDecimal();

        $pi  = '3.1415926535';
        $min = '-10000000000';
        $max = '10000000000';

        /** @var \eTraxis\Repository\DecimalValuesRepository $repository */
        $repository = $this->doctrine->getRepository(DecimalValue::class);

        /** @var DecimalValue $value */
        $value = $repository->findOneBy(['value' => $pi]);

        $field->setDefaultValue($pi);
        $this->assertEquals($pi, $field->getDefaultValue());
        $this->assertEquals($value->getId(), $this->object->getDefaultValue());

        $field->setDefaultValue($min);
        $this->assertEquals(DecimalField::MIN_VALUE, $field->getDefaultValue());

        $field->setDefaultValue($max);
        $this->assertEquals(DecimalField::MAX_VALUE, $field->getDefaultValue());

        $field->setDefaultValue(null);
        $this->assertNull($field->getDefaultValue());
        $this->assertNull($this->object->getDefaultValue());
    }
}
