<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
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
        $this->assertNull($this->object->getParentId());
    }

    public function testChildId()
    {
        $this->assertNull($this->object->getChildId());
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
