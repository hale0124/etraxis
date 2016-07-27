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

    public function testValidate()
    {
        $this->object->setCheck('(\d{3})-(\d{3})-(\d{4})');

        self::assertTrue($this->object->validate('123-456-7890'));
        self::assertFalse($this->object->validate('123-456-789'));
        self::assertFalse($this->object->validate('abc-def-ghij'));
        self::assertFalse($this->object->validate(''));
        self::assertFalse($this->object->validate(null));
    }

    public function testTransform()
    {
        $expected = [
            '123-456-7890' => '(123) 456-7890',
            '123-456-789'  => '123-456-789',
            'abc-def-ghij' => 'abc-def-ghij',
            ''             => '',
            null           => null,
        ];

        $this->object->setSearch('(\d{3})-(\d{3})-(\d{4})');
        $this->object->setReplace('($1) $2-$3');

        foreach ($expected as $from => $to) {
            self::assertEquals($to, $this->object->transform($from));
        }
    }

    public function testTransform1()
    {
        $expected = '123-456-7890';

        $this->object->setSearch('(\d{3})-(\d{3})-(\d{4})');
        self::assertEquals($expected, $this->object->transform($expected));

        $this->object->setReplace('($1) $2-$3');
        self::assertNotEquals($expected, $this->object->transform($expected));
    }

    public function testTransform2()
    {
        $expected = '123-456-7890';

        $this->object->setReplace('($1) $2-$3');
        self::assertEquals($expected, $this->object->transform($expected));

        $this->object->setSearch('(\d{3})-(\d{3})-(\d{4})');
        self::assertNotEquals($expected, $this->object->transform($expected));
    }
}
