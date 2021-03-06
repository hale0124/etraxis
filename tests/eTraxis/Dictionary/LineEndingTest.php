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

class LineEndingTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $expected = [
            LineEnding::WINDOWS,
            LineEnding::UNIX,
            LineEnding::MACINTOSH,
        ];

        self::assertEquals($expected, LineEnding::keys());
    }

    public function testGetDelimiter()
    {
        self::assertEquals("\r\n", LineEnding::get(LineEnding::WINDOWS));
        self::assertEquals("\n", LineEnding::get(LineEnding::UNIX));
        self::assertEquals("\r", LineEnding::get(LineEnding::MACINTOSH));
    }
}
