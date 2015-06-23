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

class CallTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testCallMethodSuccess()
    {
        $object   = new MyTestClassStub();
        $expected = '<' . PHP_VERSION . '>';

        $this->assertEquals($expected, $object->getVersion('<', '>'));
    }

    /**
     * @expectedException \Exception
     */
    public function testCallMethodFailure()
    {
        $object = new MyTestClassStub();
        $object->getUnknown();
    }
}
