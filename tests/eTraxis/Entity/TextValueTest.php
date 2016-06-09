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

class TextValueTest extends TransactionalTestCase
{
    /** @var TextValue */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getRepository(TextValue::class)->findOneBy([
            'token' => 'b638cc32b8362077f29b02b60d972381',
        ]);
    }

    public function testConstruct()
    {
        $expected = str_pad(null, 4000, '_');
        $value    = new TextValue($expected);

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
        $expected = 'Delivery failed due to the comet running out of ice.';
        self::assertEquals($expected, $this->object->getValue());
    }
}
