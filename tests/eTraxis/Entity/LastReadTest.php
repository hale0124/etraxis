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
    private $object;

    protected function setUp()
    {
        $this->object = new LastRead();
    }

    public function testRecord()
    {
        $this->object->setRecord($record = new Record());
        self::assertSame($record, $this->object->getRecord());
    }

    public function testUser()
    {
        $this->object->setUser($user = new User());
        self::assertSame($user, $this->object->getUser());
    }

    public function testReadAt()
    {
        $expected = time();
        $this->object->setReadAt($expected);
        self::assertEquals($expected, $this->object->getReadAt());
    }
}
