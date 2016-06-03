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
use eTraxis\Entity\Field;

class CheckboxFieldTest extends \PHPUnit_Framework_TestCase
{
    /** @var Field */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new Field();
        $this->object->setType(FieldType::CHECKBOX);
    }

    public function testSupportedKeys()
    {
        $expected = ['defaultValue'];

        $field = $this->object->asCheckbox();

        $reflection = new \ReflectionObject($field);
        $method     = $reflection->getMethod('getSupportedKeys');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($field, []);

        self::assertCount(count($expected), $actual);

        foreach ($expected as $key) {
            self::assertContains($key, $actual);
        }
    }

    public function testDefaultValue()
    {
        $field = $this->object->asCheckbox();

        $field->setDefaultValue(true);
        self::assertTrue($field->getDefaultValue());
        self::assertEquals(1, $this->object->getParameters()->getDefaultValue());

        $field->setDefaultValue(false);
        self::assertFalse($field->getDefaultValue());
        self::assertEquals(0, $this->object->getParameters()->getDefaultValue());
    }
}
