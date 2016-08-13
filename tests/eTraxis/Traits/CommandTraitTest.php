<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Traits;

class CommandTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $object = new CommandStub();

        self::assertEquals(1, $object->property);
    }

    public function testInitialization()
    {
        $object = new CommandStub(['property' => 2]);

        self::assertEquals(2, $object->property);
    }

    public function testInitializationExtra()
    {
        $object = new CommandStub(['property' => 2], ['property' => 3]);

        self::assertEquals(3, $object->property);
    }
}
