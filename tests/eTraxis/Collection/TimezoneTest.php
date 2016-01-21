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

class TimezoneTest extends BaseTestCase
{
    public function testGetCollection()
    {
        $collection = Timezone::getCollection();

        $this->assertContains('UTC', $collection);
        $this->assertContains('Asia/Vladivostok', $collection);
        $this->assertContains('Pacific/Auckland', $collection);
    }
}
