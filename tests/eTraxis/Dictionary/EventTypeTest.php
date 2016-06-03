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

class EventTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $expected = [
            EventType::RECORD_CREATED,
            EventType::RECORD_EDITED,
            EventType::RECORD_ASSIGNED,
            EventType::STATE_CHANGED,
            EventType::RECORD_POSTPONED,
            EventType::RECORD_RESUMED,
            EventType::RECORD_CLONED,
            EventType::RECORD_REOPENED,
            EventType::PUBLIC_COMMENT,
            EventType::PRIVATE_COMMENT,
            EventType::FILE_ATTACHED,
            EventType::FILE_DELETED,
            EventType::SUBRECORD_ATTACHED,
            EventType::SUBRECORD_DETACHED,
        ];

        self::assertEquals($expected, EventType::keys());
    }
}
