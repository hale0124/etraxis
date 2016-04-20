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
        self::assertEquals("\t", CsvDelimiter::get(CsvDelimiter::TAB));
        self::assertEquals(' ', CsvDelimiter::get(CsvDelimiter::SPACE));
        self::assertEquals(',', CsvDelimiter::get(CsvDelimiter::COMMA));
        self::assertEquals(':', CsvDelimiter::get(CsvDelimiter::COLON));
        self::assertEquals(';', CsvDelimiter::get(CsvDelimiter::SEMICOLON));
        self::assertEquals('|', CsvDelimiter::get(CsvDelimiter::VERTICAL_LINE));
    }
}
