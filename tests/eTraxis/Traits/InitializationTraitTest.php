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

class InitializationTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialization()
    {
        $expected = time();

        $object = new MyTestClassStub([
            'property' => $expected,
        ]);

        $this->assertEquals($expected, $object->property);
    }
}
