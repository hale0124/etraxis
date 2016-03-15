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

    public function testRecordId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setRecordId($expected);
        $this->assertEquals($expected, $this->object->getRecordId());
    }

    public function testUserId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setUserId($expected);
        $this->assertEquals($expected, $this->object->getUserId());
    }

    public function testRecord()
    {
        $this->object->setRecord($record = new Record());
        $this->assertSame($record, $this->object->getRecord());
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
