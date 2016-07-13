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

class LegacyTemplatePermissionTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        self::assertEquals(TemplatePermission::CREATE_RECORDS, Legacy\TemplatePermission::get(0x0001));
        self::assertEquals(TemplatePermission::EDIT_RECORDS, Legacy\TemplatePermission::get(0x0002));
        self::assertEquals(TemplatePermission::POSTPONE_RECORDS, Legacy\TemplatePermission::get(0x0004));
        self::assertEquals(TemplatePermission::RESUME_RECORDS, Legacy\TemplatePermission::get(0x0008));
        self::assertEquals(TemplatePermission::REASSIGN_RECORDS, Legacy\TemplatePermission::get(0x0010));
        self::assertEquals(TemplatePermission::REOPEN_RECORDS, Legacy\TemplatePermission::get(0x0020));
        self::assertEquals(TemplatePermission::ADD_COMMENTS, Legacy\TemplatePermission::get(0x0040));
        self::assertEquals(TemplatePermission::ATTACH_FILES, Legacy\TemplatePermission::get(0x0080));
        self::assertEquals(TemplatePermission::DELETE_FILES, Legacy\TemplatePermission::get(0x0100));
        self::assertEquals(TemplatePermission::PRIVATE_COMMENTS, Legacy\TemplatePermission::get(0x0200));
        self::assertEquals(TemplatePermission::SEND_REMINDERS, Legacy\TemplatePermission::get(0x0400));
        self::assertEquals(TemplatePermission::DELETE_RECORDS, Legacy\TemplatePermission::get(0x0800));
        self::assertEquals(TemplatePermission::ATTACH_SUBRECORDS, Legacy\TemplatePermission::get(0x1000));
        self::assertEquals(TemplatePermission::DETACH_SUBRECORDS, Legacy\TemplatePermission::get(0x2000));
        self::assertEquals(TemplatePermission::VIEW_RECORDS, Legacy\TemplatePermission::get(0x40000000));
    }
}
