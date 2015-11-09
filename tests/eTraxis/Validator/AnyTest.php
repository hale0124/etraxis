<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Validator;

use eTraxis\Tests\BaseTestCase;
use Symfony\Component\Validator\Constraints as Assert;

class AnyStub
{
    /**
     * @Assert\NotNull()
     * @eTraxis\Validator\Any({
     *     @Assert\LessThanOrEqual(value = "-100"),
     *     @Assert\GreaterThanOrEqual(value = "100")
     * })
     */
    public $id = null;
}

class AnyTest extends BaseTestCase
{
    public function testEmpty()
    {
        $object = new AnyStub();

        $this->assertNotCount(0, $this->validator->validate($object));
    }

    public function testInvalid()
    {
        $object     = new AnyStub();
        $object->id = rand(-99, 99);

        $this->assertNotCount(0, $this->validator->validate($object));
    }

    public function testSuccess1()
    {
        $object     = new AnyStub();
        $object->id = 100;

        $this->assertCount(0, $this->validator->validate($object));
    }

    public function testSuccess2()
    {
        $object     = new AnyStub();
        $object->id = -100;

        $this->assertCount(0, $this->validator->validate($object));
    }
}
