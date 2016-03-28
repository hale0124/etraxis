<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace Symfony\Component\Validator\Constraints;

use eTraxis\Tests\BaseTestCase;

class AnyStub
{
    /**
     * @NotNull()
     * @Any({
     *     @LessThanOrEqual(value = "-100"),
     *     @GreaterThanOrEqual(value = "100")
     * })
     */
    public $id;
}

class AnyTest extends BaseTestCase
{
    public function testEmpty()
    {
        $object = new AnyStub();

        self::assertNotCount(0, $this->validator->validate($object));
    }

    public function testInvalid()
    {
        $object     = new AnyStub();
        $object->id = mt_rand(-99, 99);

        self::assertNotCount(0, $this->validator->validate($object));
    }

    public function testSuccess1()
    {
        $object     = new AnyStub();
        $object->id = 100;

        self::assertCount(0, $this->validator->validate($object));
    }

    public function testSuccess2()
    {
        $object     = new AnyStub();
        $object->id = -100;

        self::assertCount(0, $this->validator->validate($object));
    }
}
