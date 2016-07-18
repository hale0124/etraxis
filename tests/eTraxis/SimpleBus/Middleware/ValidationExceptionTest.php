<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Middleware;

class ValidationExceptionTest extends \PHPUnit_Framework_TestCase
{
    /** @var ValidationException */
    private $object;

    protected function setUp()
    {
        $this->object = new ValidationException(['Error #1', 'foo' => 'Error #2']);
    }

    public function testDefault()
    {
        self::assertEquals(400, $this->object->getStatusCode());
    }

    public function testCountable()
    {
        self::assertCount(2, $this->object);
    }

    public function testIterator()
    {
        $messages = [0 => 'Error #1', 'foo' => 'Error #2'];

        foreach ($this->object as $key => $value) {
            self::assertEquals($messages[$key], $value);
        }
    }

    public function testToArray()
    {
        $messages = ['Error #1', 'foo' => 'Error #2'];

        self::assertEquals($messages, $this->object->toArray());
    }
}
