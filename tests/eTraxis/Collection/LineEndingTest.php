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

class LineEndingTest extends BaseTestCase
{
    public function testGetCollection()
    {
        $expected = [
            LineEnding::WINDOWS,
            LineEnding::UNIX,
            LineEnding::MACINTOSH,
        ];

        $this->assertEquals($expected, array_keys(LineEnding::getCollection()));
    }

    public function testGetDelimiter()
    {
        $this->assertEquals("\r\n", LineEnding::getLineEnding(LineEnding::WINDOWS));
        $this->assertEquals("\n", LineEnding::getLineEnding(LineEnding::UNIX));
        $this->assertEquals("\r", LineEnding::getLineEnding(LineEnding::MACINTOSH));
    }
}
