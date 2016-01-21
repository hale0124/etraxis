<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Collection;

use eTraxis\Entity\State;
use eTraxis\Tests\BaseTestCase;

class StateTypeTest extends BaseTestCase
{
    public function testGetCollection()
    {
        $expected = [
            State::TYPE_INITIAL,
            State::TYPE_TRANSIENT,
            State::TYPE_FINAL,
        ];

        $this->assertEquals($expected, array_keys(StateType::getCollection()));
    }
}
