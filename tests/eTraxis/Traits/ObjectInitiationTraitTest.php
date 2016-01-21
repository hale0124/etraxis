<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Traits;

class ObjectStub
{
    use ObjectInitiationTrait;

    public $property = 1;
}

class ObjectInitiationTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $object = new ObjectStub();

        $this->assertEquals(1, $object->property);
    }

    public function testInitialization()
    {
        $object = new ObjectStub([
            'property' => 2,
        ]);

        $this->assertEquals(2, $object->property);
    }
}
