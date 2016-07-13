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

class LegacyLocaleTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        self::assertEquals(count(Locale::all()), count(Legacy\Locale::all()));

        foreach (Legacy\Locale::values() as $locale) {
            self::assertTrue(Locale::has($locale));
        }
    }
}
