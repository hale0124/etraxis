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

class RequiredKeyStub
{
    /**
     * @eTraxis\Validator\ForeignKeyConstraint(entity = "eTraxis:User")
     */
    public $id = null;
}

class OptionalKeyStub
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
        $object = new OptionalKeyStub();

        $this->assertCount(0, $this->validator->validate($object));
    }

    public function testRequiredEmpty()
    {
        $object = new RequiredKeyStub();

        $this->assertNotCount(0, $this->validator->validate($object));
    }

    public function testInvalid()
    {
        $object     = new RequiredKeyStub();
        $object->id = 'test';

        $this->assertNotCount(0, $this->validator->validate($object));
    }

    public function testUnknown()
    {
        $object     = new RequiredKeyStub();
        $object->id = $this->getMaxId();

        $this->assertNotCount(0, $this->validator->validate($object));
    }

    public function testSuccess()
    {
        $user = $this->findUser('artem');

        $object     = new RequiredKeyStub();
        $object->id = $user->getId();

        $this->assertCount(0, $this->validator->validate($object));
    }
}
