<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

class ChildTest extends \PHPUnit_Framework_TestCase
{
    /** @var Child */
    private $object = null;

    protected function setUp()
    {
        $this->object = new Child();
    }

    public function testParentId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setParentId($expected);
        $this->assertEquals($expected, $this->object->getParentId());
    }

    public function testChildId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setChildId($expected);
        $this->assertEquals($expected, $this->object->getChildId());
    }

    public function testIsDependency()
    {
        $this->object->setDependency(false);
        $this->assertFalse($this->object->isDependency());

        $this->object->setDependency(true);
        $this->assertTrue($this->object->isDependency());
    }

    public function testParent()
    {
        $this->object->setParent($issue = new Issue());
        $this->assertSame($issue, $this->object->getParent());
    }

    public function testChild()
    {
        $this->object->setChild($issue = new Issue());
        $this->assertSame($issue, $this->object->getChild());
    }
}
