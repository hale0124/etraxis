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

use eTraxis\Tests\TransactionalTestCase;
use eTraxis\Traits\ReflectionTrait;

class StringValueTest extends TransactionalTestCase
{
    use ReflectionTrait;

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

        self::assertEquals(md5($expected), $this->getProperty($value, 'token'));
        self::assertEquals($expected, $value->getValue());
    }

    public function testId()
    {
        $expected = random_int(1, PHP_INT_MAX);
        $this->setProperty($this->object, 'id', $expected);
        self::assertEquals($expected, $this->object->getId());
    }

    public function testValue()
    {
        $expected = 'Luna Park, Moon';
        self::assertEquals($expected, $this->object->getValue());
    }
}
