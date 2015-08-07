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
        $this->assertNull($this->object->getIssueId());
    }

    public function testUserId()
    {
        $this->assertNull($this->object->getUserId());
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
