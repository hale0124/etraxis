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

namespace eTraxis\Traits;

class GetTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSet()
    {
        $object = new MyTestClassStub();

        $this->assertTrue(isset($object->property));
        $this->assertFalse(isset($object->unknown));
    }

    public function testGetPropertySuccess()
    {
        $object   = new MyTestClassStub();
        $expected = mt_rand();
        $object->setProperty($expected);

        $this->assertEquals($expected, $object->property);
    }

    /**
     * @expectedException \Exception
     */
    public function testGetPropertyFailure()
    {
        $object = new MyTestClassStub();

        /** @noinspection PhpUndefinedFieldInspection */
        echo($object->unknown);
    }
}
