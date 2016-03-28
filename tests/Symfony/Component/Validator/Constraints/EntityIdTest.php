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

class EntityIdStub
{
    /**
     * @EntityId()
     */
    public $id;
}

class EntityIdTest extends BaseTestCase
{
    public function testEmpty()
    {
        $object = new EntityIdStub();

        self::assertCount(0, $this->validator->validate($object));
    }

    public function testInvalid()
    {
        $object     = new EntityIdStub();
        $object->id = 'test';

        self::assertNotCount(0, $this->validator->validate($object));
    }

    public function testSuccess()
    {
        $object     = new EntityIdStub();
        $object->id = mt_rand(1, $this->getMaxId());

        self::assertCount(0, $this->validator->validate($object));
    }
}
