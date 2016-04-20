<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Dictionary;

use eTraxis\Tests\BaseTestCase;

class LocaleTest extends BaseTestCase
{
    public function testDictionary()
    {
        self::assertContains('ru', Locale::keys());
        self::assertEquals('Русский', Locale::get('ru'));
    }
}
