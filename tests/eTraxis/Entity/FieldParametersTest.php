<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

class FieldParametersTest extends \PHPUnit_Framework_TestCase
{
    /** @var FieldParameters */
    private $object;

    protected function setUp()
    {
        $this->object = new FieldParameters();
    }

    public function testParameter1()
    {
        $expected = random_int(1, PHP_INT_MAX);
        $this->object->setParameter1($expected);
        self::assertEquals($expected, $this->object->getParameter1());
    }

    public function testParameter2()
    {
        $expected = random_int(1, PHP_INT_MAX);
        $this->object->setParameter2($expected);
        self::assertEquals($expected, $this->object->getParameter2());
    }

    public function testDefaultValue()
    {
        $expected = random_int(1, PHP_INT_MAX);
        $this->object->setDefaultValue($expected);
        self::assertEquals($expected, $this->object->getDefaultValue());
    }
}
