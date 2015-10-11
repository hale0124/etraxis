<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Traits;

class CommandStub
{
    use CommandBusTrait;

    public $property = 1;
}

class CommandBusTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $command = new CommandStub();

        $this->assertEquals(1, $command->property);
    }

    public function testInitialization()
    {
        $command = new CommandStub([
            'property' => 2,
        ]);

        $this->assertEquals(2, $command->property);
    }
}
