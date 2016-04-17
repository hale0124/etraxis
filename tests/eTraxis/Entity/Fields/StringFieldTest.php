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
            ->setEntityManager($this->doctrine->getManager())
            ->setType(Field::TYPE_STRING)
        ;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidRepository()
    {
        new StringField($this->object, $this->doctrine->getManager()->getRepository(Field::class));
    }

    public function testSupportedKeys()
    {
        $expected = ['maxLength', 'defaultValue'];

        $field = $this->object->asString();

        $reflection = new \ReflectionObject($field);
        $method     = $reflection->getMethod('getSupportedKeys');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($field, []);

        self::assertCount(count($expected), $actual);

        foreach ($expected as $key) {
            self::assertContains($key, $actual);
        }
    }

    public function testMaxLength()
    {
        $field = $this->object->asString();

        $field->setMaxLength(100);
        self::assertEquals(100, $field->getMaxLength());
        self::assertEquals(100, $this->object->getParameters()->getParameter1());

        $field->setMaxLength(0);
        self::assertEquals(StringField::MIN_LENGTH, $field->getMaxLength());
        self::assertEquals(StringField::MIN_LENGTH, $this->object->getParameters()->getParameter1());

        $field->setMaxLength(PHP_INT_MAX);
        self::assertEquals(StringField::MAX_LENGTH, $field->getMaxLength());
        self::assertEquals(StringField::MAX_LENGTH, $this->object->getParameters()->getParameter1());
    }

    public function testDefaultValue()
    {
        $field = $this->object->asString();

        /** @var \eTraxis\Repository\CustomValuesRepositoryInterface $repository */
        $repository = $this->doctrine->getRepository(StringValue::class);

        /** @var StringValue $value */
        $value = $repository->findOneBy(['value' => 'Planet Express headquarters']);

        $field->setDefaultValue($value->getValue());
        self::assertEquals($value->getValue(), $field->getDefaultValue());
        self::assertEquals($value->getId(), $this->object->getParameters()->getDefaultValue());

        $huge = str_pad(null, 1000);
        $trim = str_pad(null, StringField::MAX_LENGTH);

        $field->setDefaultValue($huge);
        self::assertEquals($trim, $field->getDefaultValue());

        $field->setDefaultValue(null);
        self::assertNull($field->getDefaultValue());
        self::assertNull($this->object->getParameters()->getDefaultValue());
    }
}
