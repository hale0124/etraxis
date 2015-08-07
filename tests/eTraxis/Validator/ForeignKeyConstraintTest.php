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

class RequiredStub
{
    /**
     * @eTraxis\Validator\ForeignKeyConstraint(entity = "eTraxis:User")
     */
    public $id = null;
}

class OptionalStub
{
    /**
     * @eTraxis\Validator\ForeignKeyConstraint(entity = "eTraxis:User", required = false)
     */
    public $id = null;
}

class ForeignKeyConstraintTest extends BaseTestCase
{
    public function testOptionalEmpty()
    {
        $object = new OptionalStub();

        $this->assertCount(0, $this->validator->validate($object));
    }

    public function testRequiredEmpty()
    {
        $object = new RequiredStub();

        $this->assertNotCount(0, $this->validator->validate($object));
    }

    public function testInvalid()
    {
        $object = new RequiredStub();
        $object->id = 'test';

        $this->assertNotCount(0, $this->validator->validate($object));
    }

    public function testUnknown()
    {
        $object = new RequiredStub();
        $object->id = PHP_INT_MAX;

        $this->assertNotCount(0, $this->validator->validate($object));
    }

    public function testSuccess()
    {
        $user = $this->findUser('artem');

        $object = new RequiredStub();
        $object->id = $user->getId();

        $this->assertCount(0, $this->validator->validate($object));
    }
}
