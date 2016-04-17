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
            ->injectDependencies($this->doctrine->getManager())
            ->setType(Field::TYPE_DECIMAL)
        ;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidRepository()
    {
        new DecimalField($this->object, $this->doctrine->getManager()->getRepository(Field::class));
    }

    public function testSupportedKeys()
    {
        $expected = ['minValue', 'maxValue', 'defaultValue'];

        $field = $this->object->asDecimal();

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
        $field = $this->object->asDecimal();

        $pi  = '3.1415926535';
        $min = '-10000000000';
        $max = '10000000000';

        /** @var \eTraxis\Repository\CustomValuesRepositoryInterface $repository */
        $repository = $this->doctrine->getRepository(DecimalValue::class);

        /** @var DecimalValue $value */
        $value = $repository->findOneBy(['value' => $pi]);

        $field->setMinValue($pi);
        self::assertEquals($pi, $field->getMinValue());
        self::assertEquals($value->getId(), $this->object->getParameters()->getParameter1());

        $field->setMinValue($min);
        self::assertEquals(DecimalField::MIN_VALUE, $field->getMinValue());

        $field->setMinValue($max);
        self::assertEquals(DecimalField::MAX_VALUE, $field->getMinValue());
    }

    public function testMaxValue()
    {
        $field = $this->object->asDecimal();

        $pi  = '3.1415926535';
        $min = '-10000000000';
        $max = '10000000000';

        /** @var \eTraxis\Repository\CustomValuesRepositoryInterface $repository */
        $repository = $this->doctrine->getRepository(DecimalValue::class);

        /** @var DecimalValue $value */
        $value = $repository->findOneBy(['value' => $pi]);

        $field->setMaxValue($pi);
        self::assertEquals($pi, $field->getMaxValue());
        self::assertEquals($value->getId(), $this->object->getParameters()->getParameter2());

        $field->setMaxValue($min);
        self::assertEquals(DecimalField::MIN_VALUE, $field->getMaxValue());

        $field->setMaxValue($max);
        self::assertEquals(DecimalField::MAX_VALUE, $field->getMaxValue());
    }

    public function testDefaultValue()
    {
        $field = $this->object->asDecimal();

        $pi  = '3.1415926535';
        $min = '-10000000000';
        $max = '10000000000';

        /** @var \eTraxis\Repository\CustomValuesRepositoryInterface $repository */
        $repository = $this->doctrine->getRepository(DecimalValue::class);

        /** @var DecimalValue $value */
        $value = $repository->findOneBy(['value' => $pi]);

        $field->setDefaultValue($pi);
        self::assertEquals($pi, $field->getDefaultValue());
        self::assertEquals($value->getId(), $this->object->getParameters()->getDefaultValue());

        $field->setDefaultValue($min);
        self::assertEquals(DecimalField::MIN_VALUE, $field->getDefaultValue());

        $field->setDefaultValue($max);
        self::assertEquals(DecimalField::MAX_VALUE, $field->getDefaultValue());

        $field->setDefaultValue(null);
        self::assertNull($field->getDefaultValue());
        self::assertNull($this->object->getParameters()->getDefaultValue());
    }
}
