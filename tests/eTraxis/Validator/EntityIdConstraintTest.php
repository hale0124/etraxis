<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Validator;

use eTraxis\Tests\BaseTestCase;

class EntityIdStub
{
    /**
     * @eTraxis\Validator\EntityIdConstraint()
     */
    public $id = null;
}

class EntityIdConstraintTest extends BaseTestCase
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
