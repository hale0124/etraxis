<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Record;

use eTraxis\Dictionary\FieldType;

class RecordStateTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $expected = 'New';

        $state = new RecordState($expected);

        self::assertEquals($expected, $state->getName());
    }

    public function testEmptyFields()
    {
        $state = new RecordState('New');

        self::assertCount(0, $state->getFields());
    }

    public function testNonEmptyFields()
    {
        $state = new RecordState('New', [
            new RecordField('Version', FieldType::STRING),
            new RecordField('Description', FieldType::TEXT),
        ]);

        self::assertCount(2, $state->getFields());
    }
}
