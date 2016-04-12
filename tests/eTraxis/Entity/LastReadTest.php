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

use AltrEgo\AltrEgo;

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
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $this->object->setRecord($record = new Record());
        self::assertEquals($record, $object->record);
    }

    public function testUser()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $this->object->setUser($user = new User());
        self::assertEquals($user, $object->user);
    }
}
