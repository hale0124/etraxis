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

class StringValueTest extends TransactionalTestCase
{
    /** @var StringValue */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getRepository(StringValue::class)->findOneBy([
            'token' => '38bed8299c0637cfa4412f8ba3e9a50f',
        ]);
    }

    public function testConstruct()
    {
        $expected = str_pad(null, 150, '_');
        $value    = new StringValue($expected);

        /** @var \StdClass $object */
        $object = AltrEgo::create($value);

        self::assertEquals(md5($expected), $object->token);
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
        $expected = 'Luna Park, Moon';
        self::assertEquals($expected, $this->object->getValue());
    }
}
