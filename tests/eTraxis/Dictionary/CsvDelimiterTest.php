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

class CsvDelimiterTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $expected = [
            CsvDelimiter::TAB,
            CsvDelimiter::SPACE,
            CsvDelimiter::COMMA,
            CsvDelimiter::COLON,
            CsvDelimiter::SEMICOLON,
            CsvDelimiter::VERTICAL_LINE,
        ];

        self::assertEquals($expected, CsvDelimiter::keys());
    }

    public function testGetDelimiter()
    {
        self::assertEquals("\t", CsvDelimiter::getDelimiter(CsvDelimiter::TAB));
        self::assertEquals(' ', CsvDelimiter::getDelimiter(CsvDelimiter::SPACE));
        self::assertEquals(',', CsvDelimiter::getDelimiter(CsvDelimiter::COMMA));
        self::assertEquals(':', CsvDelimiter::getDelimiter(CsvDelimiter::COLON));
        self::assertEquals(';', CsvDelimiter::getDelimiter(CsvDelimiter::SEMICOLON));
        self::assertEquals('|', CsvDelimiter::getDelimiter(CsvDelimiter::VERTICAL_LINE));
    }
}
