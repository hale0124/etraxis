<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

class DecimalValueTest extends \PHPUnit_Framework_TestCase
{
    /** @var DecimalValue */
    private $object = null;

    protected function setUp()
    {
        $this->object = new DecimalValue();
    }

    public function testId()
    {
        $this->assertEquals(null, $this->object->getId());
    }

    public function testValue()
    {
        $expected = '1234567890.0987654321';
        $this->object->setValue($expected);
        $this->assertEquals($expected, $this->object->getValue());
    }
}
