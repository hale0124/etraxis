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

class FilterTest extends \PHPUnit_Framework_TestCase
{
    /** @var Filter */
    private $object = null;

    protected function setUp()
    {
        $this->object = new Filter();
    }

    public function testId()
    {
        $this->assertEquals(null, $this->object->getId());
    }

    public function testUserId()
    {
        $this->assertNull($this->object->getUserId());
    }

    public function testName()
    {
        $expected = 'Name';
        $this->object->setName($expected);
        $this->assertEquals($expected, $this->object->getName());
    }

    public function testType()
    {
        $expected = Filter::TYPE_SEL_STATES;
        $this->object->setType($expected);
        $this->assertEquals($expected, $this->object->getType());
    }

    public function testFlags()
    {
        $expected = Filter::FLAG_CREATED_BY | Filter::FLAG_ACTIVE;
        $this->object->setFlags($expected);
        $this->assertEquals($expected, $this->object->getFlags());
    }

    public function testParameter()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setParameter($expected);
        $this->assertEquals($expected, $this->object->getParameter());
    }

    public function testUser()
    {
        $this->object->setUser($user = new User());
        $this->assertSame($user, $this->object->getUser());
    }
}
