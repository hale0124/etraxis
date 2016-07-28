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

class TimezoneTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        self::assertTrue(Timezone::has('UTC'));
        self::assertTrue(Timezone::has('Asia/Vladivostok'));
        self::assertTrue(Timezone::has('Pacific/Auckland'));
    }
}
