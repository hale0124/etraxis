<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Record;

use eTraxis\Dictionary\EventType;
use eTraxis\Tests\ControllerTestCase;

class RecordEventTest extends ControllerTestCase
{
    /** @var RecordEvent */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new RecordEvent($this->findUser('hubert'), EventType::RECORD_CREATED, 123456789, 'New');
    }

    public function testUser()
    {
        $expected = 'Hubert J. Farnsworth';
        self::assertEquals($expected, $this->object->getUser()->getFullname());
    }

    public function testType()
    {
        $expected = EventType::RECORD_CREATED;
        self::assertEquals($expected, $this->object->getType());
    }

    public function testCreatedAt()
    {
        $expected = 123456789;
        self::assertEquals($expected, $this->object->getCreatedAt());
    }

    public function testParameter()
    {
        $expected = 'New';
        self::assertEquals($expected, $this->object->getParameter());
    }
}
