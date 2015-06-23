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


namespace eTraxis\Traits;

class SetTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testSetPropertySuccess()
    {
        $object           = new MyTestClassStub();
        $expected         = mt_rand();
        $object->property = $expected;

        $this->assertEquals($expected, $object->getProperty());
    }

    /**
     * @expectedException \Exception
     */
    public function testSetPropertyFailure()
    {
        $object          = new MyTestClassStub();
        $expected        = mt_rand();
        $object->unknown = $expected;
    }
}
