<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

use eTraxis\Tests\TransactionalTestCase;

class ListItemTest extends TransactionalTestCase
{
    /** @var ListItem */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getRepository(ListItem::class)->findOneBy([
            'text' => 'Season 1',
        ]);
    }

    public function testConstruct()
    {
        $item = new ListItem($this->object->getField());
        self::assertEquals($this->object->getField(), $item->getField());
    }

    public function testField()
    {
        $expected = 'Season';
        self::assertEquals($expected, $this->object->getField()->getName());
    }

    public function testValue()
    {
        $expected = random_int(1, PHP_INT_MAX);
        $this->object->setValue($expected);
        self::assertEquals($expected, $this->object->getValue());
    }

    public function testText()
    {
        $expected = str_pad('_', 50, '_');
        $this->object->setText($expected);
        self::assertEquals($expected, $this->object->getText());
    }
}
