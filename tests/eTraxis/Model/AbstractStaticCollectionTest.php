<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Model;

class TestStaticCollection extends AbstractStaticCollection
{
    public static function getCollection()
    {
        return [
            'mon' => 'Monday',
            'tue' => 'Tuesday',
            'wed' => 'Wednesday',
            'thu' => 'Thursday',
            'fri' => 'Friday',
            'sat' => 'Saturday',
            'sun' => 'Sunday',
        ];
    }
}

class AbstractStaticCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAllKeys()
    {
        $expected = [
            'mon',
            'tue',
            'wed',
            'thu',
            'fri',
            'sat',
            'sun',
        ];

        $this->assertEquals($expected, TestStaticCollection::getAllKeys());
    }

    public function testGetAllValues()
    {
        $expected = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        ];

        $this->assertEquals($expected, TestStaticCollection::getAllValues());
    }

    public function testGetValue()
    {
        $this->assertEquals('Friday', TestStaticCollection::getValue('fri'));
        $this->assertNull(TestStaticCollection::getValue('day'));
    }
}
