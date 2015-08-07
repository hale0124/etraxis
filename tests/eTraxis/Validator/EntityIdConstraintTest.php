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

class RequiredIdStub
{
    /**
     * @eTraxis\Validator\EntityIdConstraint()
     */
    public $id = null;
}

class OptionalIdStub
{
    /**
     * @eTraxis\Validator\EntityIdConstraint(required = false)
     */
    public $id = null;
}

class EntityIdConstraintTest extends BaseTestCase
{
    public function testOptionalEmpty()
    {
        $object = new OptionalIdStub();

        $this->assertCount(0, $this->validator->validate($object));
    }

    public function testRequiredEmpty()
    {
        $object = new RequiredIdStub();

        $this->assertNotCount(0, $this->validator->validate($object));
    }

    public function testInvalid()
    {
        $object = new RequiredIdStub();
        $object->id = 'test';

        $this->assertNotCount(0, $this->validator->validate($object));
    }

    public function testSuccess()
    {
        $object = new RequiredIdStub();
        $object->id = rand(1, PHP_INT_MAX);

        $this->assertCount(0, $this->validator->validate($object));
    }
}
