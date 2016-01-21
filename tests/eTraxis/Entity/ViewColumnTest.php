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

class ViewColumnTest extends \PHPUnit_Framework_TestCase
{
    /** @var ViewColumn */
    private $object = null;

    protected function setUp()
    {
        $this->object = new ViewColumn();
    }

    public function testId()
    {
        $this->assertEquals(null, $this->object->getId());
    }

    public function testViewId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setViewId($expected);
        $this->assertEquals($expected, $this->object->getViewId());
    }

    public function testStateName()
    {
        $expected = 'State name';
        $this->object->setStateName($expected);
        $this->assertEquals($expected, $this->object->getStateName());
    }

    public function testFieldName()
    {
        $expected = 'Field name';
        $this->object->setFieldName($expected);
        $this->assertEquals($expected, $this->object->getFieldName());
    }

    public function testType()
    {
        $expected = ViewColumn::TYPE_SUBJECT;
        $this->object->setType($expected);
        $this->assertEquals($expected, $this->object->getType());
    }

    public function testOrder()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setOrder($expected);
        $this->assertEquals($expected, $this->object->getOrder());
    }

    public function testView()
    {
        $this->object->setView($user = new View());
        $this->assertSame($user, $this->object->getView());
    }
}
