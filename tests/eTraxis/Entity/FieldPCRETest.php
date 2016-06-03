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

class FieldPCRETest extends \PHPUnit_Framework_TestCase
{
    /** @var FieldPCRE */
    private $object;

    protected function setUp()
    {
        $this->object = new FieldPCRE();
    }

    public function testCheck()
    {
        $expected = 'PCRE';
        $this->object->setCheck($expected);
        self::assertEquals($expected, $this->object->getCheck());
    }

    public function testSearch()
    {
        $expected = 'PCRE';
        $this->object->setSearch($expected);
        self::assertEquals($expected, $this->object->getSearch());
    }

    public function testReplace()
    {
        $expected = 'PCRE';
        $this->object->setReplace($expected);
        self::assertEquals($expected, $this->object->getReplace());
    }
}
