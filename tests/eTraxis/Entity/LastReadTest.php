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

class LastReadTest extends \PHPUnit_Framework_TestCase
{
    /** @var LastRead */
    private $object = null;

    protected function setUp()
    {
        $this->object = new LastRead();
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

    public function testReadAt()
    {
        $expected = time();
        $this->object->setReadAt($expected);
        $this->assertEquals($expected, $this->object->getReadAt());
    }
}
