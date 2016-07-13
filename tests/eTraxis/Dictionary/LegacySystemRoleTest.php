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

class LegacySystemRoleTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        self::assertEquals(SystemRole::ANYONE, Legacy\SystemRole::get('registered_perm'));
        self::assertEquals(SystemRole::AUTHOR, Legacy\SystemRole::get('author_perm'));
        self::assertEquals(SystemRole::RESPONSIBLE, Legacy\SystemRole::get('responsible_perm'));
    }
}
