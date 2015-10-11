<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

class TextValueTest extends \PHPUnit_Framework_TestCase
{
    /** @var TextValue */
    private $object = null;

    protected function setUp()
    {
        $this->object = new TextValue();
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
        $expected = str_pad('_', 4000, '_');
        $this->object->setValue($expected);
        $this->assertEquals($expected, $this->object->getValue());
    }
}
