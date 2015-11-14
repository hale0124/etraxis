<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Collection;

use eTraxis\Entity\State;
use eTraxis\Tests\BaseTestCase;

class StateResponsibleTest extends BaseTestCase
{
    public function testGetCollection()
    {
        $expected = [
            State::RESPONSIBLE_KEEP,
            State::RESPONSIBLE_ASSIGN,
            State::RESPONSIBLE_REMOVE,
        ];

        $this->assertEquals($expected, array_keys(StateResponsible::getCollection()));
    }
}
