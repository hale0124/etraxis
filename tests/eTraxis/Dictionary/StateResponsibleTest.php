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

class StateResponsibleTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $expected = [
            State::RESPONSIBLE_KEEP,
            State::RESPONSIBLE_ASSIGN,
            State::RESPONSIBLE_REMOVE,
        ];

        self::assertEquals($expected, StateResponsible::keys());
    }
}
