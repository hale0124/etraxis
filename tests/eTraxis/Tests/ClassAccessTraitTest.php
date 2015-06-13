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


namespace eTraxis\Tests;

class ClassAccessTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testSetPropertySuccess()
    {
        $object           = new MyTestClassStub();
        $expected         = mt_rand();
        $object->property = $expected;

        $this->assertEquals($expected, $object->getProperty());
    }

    /**
     * @expectedException \Exception
     */
    public function testSetPropertyFailure()
    {
        $object          = new MyTestClassStub();
        $expected        = mt_rand();
        $object->unknown = $expected;
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
        echo($object->unknown);
    }

    public function testCallMethodSuccess()
    {
        $object   = new MyTestClassStub();
        $expected = '<' . PHP_VERSION . '>';

        $this->assertEquals($expected, $object->getVersion('<', '>'));
    }

    /**
     * @expectedException \Exception
     */
    public function testCallMethodFailure()
    {
        $object = new MyTestClassStub();
        $object->getUnknown();
    }
}
