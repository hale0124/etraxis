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

class LocaleTest extends BaseTestCase
{
    public function testGetCollection()
    {
        $collection = Locale::getCollection();

        $this->assertArrayHasKey('ru', $collection);
        $this->assertEquals('Russian', $collection['ru']);
    }

    public function testGetTranslatedCollection()
    {
        $collection = Locale::getTranslatedCollection($this->translator);

        $this->assertArrayHasKey('ru', $collection);
        $this->assertEquals('Русский', $collection['ru']);
    }
}
