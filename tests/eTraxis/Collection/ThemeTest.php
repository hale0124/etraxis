<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Collection;

use eTraxis\Tests\BaseTestCase;

class ThemeTest extends BaseTestCase
{
    public function testGetCollection()
    {
        $collection = Theme::getCollection();

        $this->assertArrayHasKey('azure', $collection);
        $this->assertEquals('Azure', $collection['azure']);
    }
}
