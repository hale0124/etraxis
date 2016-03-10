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
    private $object = null;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Season']);
    }

    public function testDefaultValue()
    {
        $field = $this->object->asList();

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy(['key' => 3]);

        $field->setDefaultItem($item->getKey());
        $this->assertEquals($item->getKey(), $field->getDefaultItem());
        $this->assertEquals($item->getValue(), $field->getDefaultValue());
        $this->assertEquals($item->getKey(), $this->object->getDefaultValue());

        $field->setDefaultItem(null);
        $this->assertNull($field->getDefaultItem());
        $this->assertNull($field->getDefaultValue());
        $this->assertNull($this->object->getDefaultValue());
    }
}
