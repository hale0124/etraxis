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

use eTraxis\Tests\BaseTestCase;

class LineEndingTest extends BaseTestCase
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
        self::assertEquals("\r\n", LineEnding::getLineEnding(LineEnding::WINDOWS));
        self::assertEquals("\n", LineEnding::getLineEnding(LineEnding::UNIX));
        self::assertEquals("\r", LineEnding::getLineEnding(LineEnding::MACINTOSH));
    }
}
