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

class MyTestClass
{
    protected $property;

    public function setProperty($value)
    {
        $this->property = $value;
    }

    public function getProperty()
    {
        return $this->property;
    }

    protected function getVersion($a, $b)
    {
        return $a . PHP_VERSION . $b;
    }
}

/**
 * @method string getVersion
 * @property mixed $property
 */
class MyTestClassStub extends MyTestClass
{
    use ClassAccessTrait;
}

class ClassAccessTraitTest extends \PHPUnit_Framework_TestCase
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

    public function testSetPropertySuccess()
    {
        $object   = new MyTestClassStub();
        $expected = mt_rand();

        $object->property = $expected;

        $this->assertEquals($expected, $object->getProperty());
    }

    /**
     * @expectedException \Exception
     */
    public function testSetPropertyFailure()
    {
        $object   = new MyTestClassStub();
        $expected = mt_rand();

        /** @noinspection PhpUndefinedFieldInspection */
        $object->unknown = $expected;
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

        /** @noinspection PhpUndefinedMethodInspection */
        $object->getUnknown();
    }
}
