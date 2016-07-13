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

class LegacyStateResponsibleTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        self::assertEquals(StateResponsible::KEEP, Legacy\StateResponsible::get(1));
        self::assertEquals(StateResponsible::ASSIGN, Legacy\StateResponsible::get(2));
        self::assertEquals(StateResponsible::REMOVE, Legacy\StateResponsible::get(3));
    }
}
