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

use eTraxis\Entity\Record;
use eTraxis\Tests\TransactionalTestCase;

class RecordHistoryTest extends TransactionalTestCase
{
    public function testHistoryUsersAndStates()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => '200 feet of hanging rope for the hanging of multiheaded monster.',
        ]);

        $history = $record->getHistory();

        self::assertCount(4, $history);
        self::assertEquals('New', $history[0]->getParameter());
        self::assertEquals('Artem Rodygin', $history[1]->getParameter());
    }

    public function testHistoryAttachments()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject'  => 'Autoloading Standard',
            'closedAt' => null,
        ]);

        $history = $record->getHistory();

        self::assertCount(5, $history);
        self::assertEquals('example.php', $history[3]->getParameter());
        self::assertEquals('Meta Document.pdf', $history[4]->getParameter());
    }
}
