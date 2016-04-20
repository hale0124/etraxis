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

class DatabasePlatformTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $expected = [
            DatabasePlatform::MYSQL,
            DatabasePlatform::POSTGRESQL,
            DatabasePlatform::MSSQL,
            DatabasePlatform::ORACLE,
        ];

        self::assertEquals($expected, DatabasePlatform::keys());
    }
}
