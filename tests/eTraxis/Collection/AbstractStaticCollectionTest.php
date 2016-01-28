<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Collection;

use eTraxis\Tests\BaseTestCase;

class TestStaticCollection extends AbstractStaticCollection
{
    public static function getCollection()
    {
        return [
            'b_ok'     => 'button.ok',
            'b_cancel' => 'button.cancel',
            'b_yes'    => 'button.yes',
            'b_no'     => 'button.no',
        ];
    }
}

class AbstractStaticCollectionTest extends BaseTestCase
{
    public function testGetAllKeys()
    {
        $expected = [
            'b_ok',
            'b_cancel',
            'b_yes',
            'b_no',
        ];

        $this->assertEquals($expected, TestStaticCollection::getAllKeys());
    }

    public function testGetAllValues()
    {
        $expected = [
            'button.ok',
            'button.cancel',
            'button.yes',
            'button.no',
        ];

        $this->assertEquals($expected, TestStaticCollection::getAllValues());
    }

    public function testGetValue()
    {
        $this->assertEquals('button.ok', TestStaticCollection::getValue('b_ok'));
        $this->assertNull(TestStaticCollection::getValue('button.ok'));
    }
}
