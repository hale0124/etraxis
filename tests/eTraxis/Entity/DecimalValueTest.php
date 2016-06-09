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
use eTraxis\Tests\TransactionalTestCase;

class DecimalValueTest extends TransactionalTestCase
{
    /** @var DecimalValue */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getRepository(DecimalValue::class)->findOneBy([
            'value' => '3.1415926535',
        ]);
    }

    public function testConstruct()
    {
        $expected = '1234567890.0987654321';
        $value    = new DecimalValue($expected);
        self::assertEquals($expected, $value->getValue());
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
        $expected = '3.1415926535';
        self::assertEquals($expected, $this->object->getValue());
    }
}
