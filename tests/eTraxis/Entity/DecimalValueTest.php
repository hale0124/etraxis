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

class DecimalValueTest extends \PHPUnit_Framework_TestCase
{
    /** @var DecimalValue */
    private $object;

    protected function setUp()
    {
        $this->object = new DecimalValue();
    }

    public function testId()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $expected   = random_int(1, PHP_INT_MAX);
        $object->id = $expected;
        self::assertEquals($expected, $this->object->getId());
    }

    public function testValue()
    {
        $expected = '1234567890.0987654321';
        $this->object->setValue($expected);
        self::assertEquals($expected, $this->object->getValue());
    }
}
