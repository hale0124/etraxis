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

class TemplatePermissionTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $expected = [
            TemplatePermission::VIEW_RECORDS,
            TemplatePermission::CREATE_RECORDS,
            TemplatePermission::EDIT_RECORDS,
            TemplatePermission::POSTPONE_RECORDS,
            TemplatePermission::RESUME_RECORDS,
            TemplatePermission::REASSIGN_RECORDS,
            TemplatePermission::REOPEN_RECORDS,
            TemplatePermission::ADD_COMMENTS,
            TemplatePermission::PRIVATE_COMMENTS,
            TemplatePermission::ATTACH_FILES,
            TemplatePermission::DELETE_FILES,
            TemplatePermission::ATTACH_SUBRECORDS,
            TemplatePermission::DETACH_SUBRECORDS,
            TemplatePermission::SEND_REMINDERS,
            TemplatePermission::DELETE_RECORDS,
        ];

        self::assertEquals($expected, TemplatePermission::keys());
    }
}
