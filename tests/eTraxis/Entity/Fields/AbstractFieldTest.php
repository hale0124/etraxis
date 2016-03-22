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

class AbstractFieldTest extends BaseTestCase
{
    /** @var Field */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new Field();
        $this->object->setType(Field::TYPE_NUMBER)->asNumber()
            ->setMinValue(NumberField::MIN_VALUE)
            ->setMaxValue(NumberField::MAX_VALUE)
        ;
    }

    public function testOffsetExists()
    {
        $field = $this->object->asNumber();

        $this->assertTrue($field->offsetExists('defaultValue'));
        $this->assertFalse($field->offsetExists('unknownValue'));
    }

    public function testOffsetGet()
    {
        $field = $this->object->asNumber();

        $this->assertEquals(NumberField::MIN_VALUE, $field->offsetGet('minValue'));
        $this->assertEquals(NumberField::MAX_VALUE, $field->offsetGet('maxValue'));
    }

    public function testOffsetSet()
    {
        $field = $this->object->asNumber();

        $expected = mt_rand(NumberField::MIN_VALUE, NumberField::MAX_VALUE);

        $this->assertNull($field->getDefaultValue());

        $field->offsetSet('defaultValue', $expected);
        $this->assertEquals($expected, $field->getDefaultValue());
    }

    public function testOffsetUnset()
    {
        $field = $this->object->asNumber();

        $field->setDefaultValue(mt_rand(NumberField::MIN_VALUE, NumberField::MAX_VALUE));
        $this->assertNotNull($field->getDefaultValue());

        $field->offsetUnset('defaultValue');
        $this->assertNull($field->getDefaultValue());
    }
}
