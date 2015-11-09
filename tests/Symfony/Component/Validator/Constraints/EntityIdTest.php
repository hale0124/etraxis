<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
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
    public $id = null;
}

class EntityIdTest extends BaseTestCase
{
    public function testEmpty()
    {
        $object = new EntityIdStub();

        $this->assertCount(0, $this->validator->validate($object));
    }

    public function testInvalid()
    {
        $object     = new EntityIdStub();
        $object->id = 'test';

        $this->assertNotCount(0, $this->validator->validate($object));
    }

    public function testSuccess()
    {
        $object     = new EntityIdStub();
        $object->id = rand(1, $this->getMaxId());

        $this->assertCount(0, $this->validator->validate($object));
    }
}
