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

class EventTest extends \PHPUnit_Framework_TestCase
{
    /** @var Event */
    private $object = null;

    protected function setUp()
    {
        $this->object = new Event();
    }

    public function testId()
    {
        $this->assertEquals(null, $this->object->getId());
    }

    public function testIssueId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setIssueId($expected);
        $this->assertEquals($expected, $this->object->getIssueId());
    }

    public function testUserId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setUserId($expected);
        $this->assertEquals($expected, $this->object->getUserId());
    }

    public function testType()
    {
        $expected = Event::ISSUE_CREATED;
        $this->object->setType($expected);
        $this->assertEquals($expected, $this->object->getType());
    }

    public function testCreatedAt()
    {
        $expected = time();
        $this->object->setCreatedAt($expected);
        $this->assertEquals($expected, $this->object->getCreatedAt());
    }

    public function testParameter()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setParameter($expected);
        $this->assertEquals($expected, $this->object->getParameter());
    }

    public function testIssue()
    {
        $this->object->setIssue($issue = new Issue());
        $this->assertSame($issue, $this->object->getIssue());
    }

    public function testUser()
    {
        $this->object->setUser($user = new User());
        $this->assertSame($user, $this->object->getUser());
    }
}
