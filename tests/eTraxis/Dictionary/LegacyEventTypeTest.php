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

class LegacyEventTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        self::assertEquals(EventType::RECORD_CREATED, Legacy\EventType::get(1));
        self::assertEquals(EventType::RECORD_ASSIGNED, Legacy\EventType::get(2));
        self::assertEquals(EventType::RECORD_EDITED, Legacy\EventType::get(3));
        self::assertEquals(EventType::STATE_CHANGED, Legacy\EventType::get(4));
        self::assertEquals(EventType::RECORD_POSTPONED, Legacy\EventType::get(5));
        self::assertEquals(EventType::RECORD_RESUMED, Legacy\EventType::get(6));
        self::assertEquals(EventType::PUBLIC_COMMENT, Legacy\EventType::get(7));
        self::assertEquals(EventType::FILE_ATTACHED, Legacy\EventType::get(8));
        self::assertEquals(EventType::FILE_DELETED, Legacy\EventType::get(9));
        self::assertEquals(EventType::RECORD_CLONED, Legacy\EventType::get(10));
        self::assertEquals(EventType::SUBRECORD_ATTACHED, Legacy\EventType::get(11));
        self::assertEquals(EventType::SUBRECORD_DETACHED, Legacy\EventType::get(12));
        self::assertEquals(EventType::PRIVATE_COMMENT, Legacy\EventType::get(13));
        self::assertEquals(EventType::RECORD_REOPENED, Legacy\EventType::get(14));
    }
}
