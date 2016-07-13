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

class LegacyStateTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        self::assertEquals(StateType::IS_INITIAL, Legacy\StateType::get(1));
        self::assertEquals(StateType::IS_INTERIM, Legacy\StateType::get(2));
        self::assertEquals(StateType::IS_FINAL, Legacy\StateType::get(3));
    }
}
