<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Dictionary;

class LegacyTimezoneTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        self::assertNotContains(0, Legacy\Timezone::keys());
        self::assertNotContains(415, Legacy\Timezone::keys());

        self::assertContains(1, Legacy\Timezone::keys());
        self::assertEquals('Africa/Abidjan', Legacy\Timezone::get(1));

        self::assertContains(377, Legacy\Timezone::keys());
        self::assertEquals('Pacific/Auckland', Legacy\Timezone::get(377));

        self::assertContains(414, Legacy\Timezone::keys());
        self::assertEquals('UTC', Legacy\Timezone::get(414));
    }
}
