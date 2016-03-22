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
use eTraxis\Entity\StringValue;
use eTraxis\Tests\BaseTestCase;

class StringFieldTest extends BaseTestCase
{
    /** @var Field */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new Field();

        /** @noinspection PhpParamsInspection */
        $this->object
            ->setType(Field::TYPE_STRING)
            ->setStringValuesRepository($this->doctrine->getRepository(StringValue::class))
        ;
    }

    public function testSupportedKeys()
    {
        $expected = ['maxLength', 'defaultValue'];

        $field = $this->object->asString();

        $reflection = new \ReflectionObject($field);
        $method     = $reflection->getMethod('getSupportedKeys');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($field, []);

        $this->assertCount(count($expected), $actual);

        foreach ($expected as $key) {
            $this->assertContains($key, $actual);
        }
    }

    public function testMaxLength()
    {
        $field = $this->object->asString();

        $field->setMaxLength(100);
        $this->assertEquals(100, $field->getMaxLength());
        $this->assertEquals(100, $this->object->getParameter1());

        $field->setMaxLength(0);
        $this->assertEquals(StringField::MIN_LENGTH, $field->getMaxLength());
        $this->assertEquals(StringField::MIN_LENGTH, $this->object->getParameter1());

        $field->setMaxLength(PHP_INT_MAX);
        $this->assertEquals(StringField::MAX_LENGTH, $field->getMaxLength());
        $this->assertEquals(StringField::MAX_LENGTH, $this->object->getParameter1());
    }

    public function testDefaultValue()
    {
        $field = $this->object->asString();

        /** @var \eTraxis\Repository\StringValuesRepository $repository */
        $repository = $this->doctrine->getRepository(StringValue::class);

        /** @var StringValue $value */
        $value = $repository->findOneBy(['value' => 'Planet Express headquarters']);

        $field->setDefaultValue($value->getValue());
        $this->assertEquals($value->getValue(), $field->getDefaultValue());
        $this->assertEquals($value->getId(), $this->object->getDefaultValue());

        $huge = str_pad(null, 1000);
        $trim = str_pad(null, StringField::MAX_LENGTH);

        $field->setDefaultValue($huge);
        $this->assertEquals($trim, $field->getDefaultValue());

        $field->setDefaultValue(null);
        $this->assertNull($field->getDefaultValue());
        $this->assertNull($this->object->getDefaultValue());
    }
}
