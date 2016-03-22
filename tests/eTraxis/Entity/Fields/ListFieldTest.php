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
use eTraxis\Entity\ListItem;
use eTraxis\Tests\BaseTestCase;

class ListFieldTest extends BaseTestCase
{
    /** @var Field */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Season']);
    }

    public function testSupportedKeys()
    {
        $expected = ['defaultKey', 'defaultValue'];

        $field = $this->object->asList();

        $reflection = new \ReflectionObject($field);
        $method     = $reflection->getMethod('getSupportedKeys');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($field, []);

        $this->assertCount(count($expected), $actual);

        foreach ($expected as $key) {
            $this->assertContains($key, $actual);
        }
    }

    public function testDefaultKey()
    {
        $field = $this->object->asList();

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy(['key' => 3]);

        $field->setDefaultKey($item->getKey());
        $this->assertEquals($item->getKey(), $field->getDefaultKey());
        $this->assertEquals($item->getValue(), $field->getDefaultValue());
        $this->assertEquals($item->getKey(), $this->object->getDefaultValue());

        $field->setDefaultKey(null);
        $this->assertNull($field->getDefaultKey());
        $this->assertNull($field->getDefaultValue());
        $this->assertNull($this->object->getDefaultValue());
    }

    public function testDefaultValue()
    {
        $field = $this->object->asList();

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy(['key' => 3]);

        $field->setDefaultValue($item->getValue());
        $this->assertEquals($item->getKey(), $field->getDefaultKey());
        $this->assertEquals($item->getValue(), $field->getDefaultValue());
        $this->assertEquals($item->getKey(), $this->object->getDefaultValue());

        $field->setDefaultValue(null);
        $this->assertNull($field->getDefaultKey());
        $this->assertNull($field->getDefaultValue());
        $this->assertNull($this->object->getDefaultValue());
    }
}
