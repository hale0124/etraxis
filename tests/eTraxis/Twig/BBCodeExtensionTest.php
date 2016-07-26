<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Twig;

use eTraxis\Tests\ControllerTestCase;

class BBCodeExtensionTest extends ControllerTestCase
{
    /** @var BBCodeExtension */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $bbcode = $this->client->getContainer()->get('etraxis.bbcode');

        $this->object = new BBCodeExtension($bbcode);
    }

    public function testGetName()
    {
        self::assertEquals('bbcode_extension', $this->object->getName());
    }

    public function testFilters()
    {
        /** @var \Twig_SimpleFilter[] $filters */
        $filters = $this->object->getFilters();

        self::assertCount(1, $filters);
        self::assertEquals('bbcode', reset($filters)->getName());
    }

    public function testFilterBBCode()
    {
        $original = '[b]test[/b]';
        $expected = '<b>test</b>';

        self::assertEquals($expected, $this->object->filterBBCode($original));
    }
}
