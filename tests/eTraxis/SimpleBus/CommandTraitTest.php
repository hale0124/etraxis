<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------


namespace eTraxis\SimpleBus;

class CommandTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSet()
    {
        $command = new SampleCommand();

        $this->assertTrue(isset($command->property));
        $this->assertFalse(isset($command->wrong));
    }

    public function testSetSuccess()
    {
        $expected = mt_rand();

        $command = new SampleCommand();

        $command->property = $expected;

        $this->assertEquals($expected, $command->property);
    }

    /**
     * @expectedException \Exception
     */
    public function testSetFailure()
    {
        $command = new SampleCommand();

        $command->wrong = null;
    }

    public function testGetSuccess()
    {
        $expected = mt_rand();

        $command = new SampleCommand($expected);

        $this->assertEquals($expected, $command->property);
    }

    /**
     * @expectedException \Exception
     */
    public function testGetFailure()
    {
        $command = new SampleCommand();

        gettype($command->wrong);
    }
}
