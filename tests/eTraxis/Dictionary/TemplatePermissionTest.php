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

use eTraxis\Entity\Template;

class TemplatePermissionTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $expected = [
            Template::PERMIT_VIEW_RECORD,
            Template::PERMIT_CREATE_RECORD,
            Template::PERMIT_EDIT_RECORD,
            Template::PERMIT_POSTPONE_RECORD,
            Template::PERMIT_RESUME_RECORD,
            Template::PERMIT_REASSIGN_RECORD,
            Template::PERMIT_REOPEN_RECORD,
            Template::PERMIT_ADD_COMMENT,
            Template::PERMIT_ADD_FILE,
            Template::PERMIT_REMOVE_FILE,
            Template::PERMIT_PRIVATE_COMMENT,
            Template::PERMIT_SEND_REMINDER,
            Template::PERMIT_DELETE_RECORD,
            Template::PERMIT_ATTACH_SUBRECORD,
            Template::PERMIT_DETACH_SUBRECORD,
        ];

        self::assertEquals($expected, TemplatePermission::keys());
    }
}
