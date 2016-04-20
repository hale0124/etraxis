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

use eTraxis\Tests\BaseTestCase;

class SystemRoleTest extends BaseTestCase
{
    public function testDictionary()
    {
        $expected = [
            SystemRole::AUTHOR,
            SystemRole::RESPONSIBLE,
            SystemRole::REGISTERED,
        ];

        self::assertEquals($expected, SystemRole::keys());
    }
}
