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

use eTraxis\Entity\State;
use eTraxis\Tests\BaseTestCase;

class StateTypeTest extends BaseTestCase
{
    public function testDictionary()
    {
        $expected = [
            State::TYPE_INITIAL,
            State::TYPE_INTERIM,
            State::TYPE_FINAL,
        ];

        self::assertEquals($expected, StateType::keys());
    }
}
