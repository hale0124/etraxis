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

use eTraxis\Entity\Field;
use eTraxis\Tests\BaseTestCase;

class FieldAccessTest extends BaseTestCase
{
    public function testGetCollection()
    {
        $expected = [
            Field::ACCESS_DENIED,
            Field::ACCESS_READ_ONLY,
            Field::ACCESS_READ_WRITE,
        ];

        self::assertEquals($expected, array_keys(FieldAccess::getCollection()));
    }
}
