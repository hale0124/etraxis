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

class StringValueTest extends \PHPUnit_Framework_TestCase
{
    /** @var StringValue */
    private $object = null;

    protected function setUp()
    {
        $this->object = new StringValue();
    }

    public function testId()
    {
        $this->assertEquals(null, $this->object->getId());
    }

    public function testToken()
    {
        $expected = 'token';
        $this->object->setToken($expected);
        $this->assertEquals($expected, $this->object->getToken());
    }

    public function testValue()
    {
        $expected = str_pad('_', 150, '_');
        $this->object->setValue($expected);
        $this->assertEquals($expected, $this->object->getValue());
    }
}
