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
use eTraxis\Tests\TransactionalTestCase;

class ListFieldTest extends TransactionalTestCase
{
    /** @var Field */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Season']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidRepository()
    {
        new ListField($this->object, $this->doctrine->getRepository(Field::class));
    }

    public function testSupportedKeys()
    {
        $expected = ['defaultValue', 'defaultText'];

        $field = $this->object->asList();

        $reflection = new \ReflectionObject($field);
        $method     = $reflection->getMethod('getSupportedKeys');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($field, []);

        self::assertCount(count($expected), $actual);

        foreach ($expected as $key) {
            self::assertContains($key, $actual);
        }
    }

    public function testGetItems()
    {
        $expected = [
            '1' => 'Season 1',
            '2' => 'Season 2',
            '3' => 'Season 3',
            '5' => 'Season 5',
            '4' => 'Season 4',
            '6' => 'Season 6',
            '7' => 'Season 7',
        ];

        $actual = [];

        foreach ($this->object->asList()->getItems() as $item) {
            $actual[$item->getValue()] = $item->getText();
        }

        self::assertEquals($expected, $actual);
    }

    public function testDefaultValue()
    {
        $field = $this->object->asList();

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy(['value' => 3]);

        $field->setDefaultValue($item->getValue());
        self::assertEquals($item->getValue(), $field->getDefaultValue());
        self::assertEquals($item->getText(), $field->getDefaultText());
        self::assertEquals($item->getValue(), $this->object->getParameters()->getDefaultValue());

        $field->setDefaultValue(null);
        self::assertNull($field->getDefaultValue());
        self::assertNull($field->getDefaultText());
        self::assertNull($this->object->getParameters()->getDefaultValue());
    }

    public function testDefaultText()
    {
        $field = $this->object->asList();

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy(['value' => 3]);

        $field->setDefaultValue($item->getValue());
        self::assertEquals($item->getValue(), $field->getDefaultValue());
        self::assertEquals($item->getText(), $field->getDefaultText());
        self::assertEquals($item->getValue(), $this->object->getParameters()->getDefaultValue());

        $field->setDefaultValue(null);
        self::assertNull($field->getDefaultValue());
        self::assertNull($field->getDefaultText());
        self::assertNull($this->object->getParameters()->getDefaultValue());
    }
}
