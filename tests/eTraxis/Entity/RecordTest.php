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

use eTraxis\Tests\TransactionalTestCase;

class RecordTest extends TransactionalTestCase
{
    /** @var Record */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getManager()->getRepository(Record::class)->findOneBy([
            'subject' => 'Prizes for the claw crane',
        ]);
    }

    public function testId()
    {
        $record = new Record();
        self::assertNull($record->getId());
        self::assertNotNull($this->object->getId());
    }

    public function testSubject()
    {
        $expected = 'Subject';
        $this->object->setSubject($expected);
        self::assertEquals($expected, $this->object->getSubject());
    }

    public function testState()
    {
        self::assertEquals('Delivered', $this->object->getState()->getName());
    }

    public function testAuthor()
    {
        self::assertEquals('Hubert J. Farnsworth', $this->object->getAuthor()->getFullname());
    }

    public function testResponsible()
    {
        self::assertNull($this->object->getResponsible());
    }

    public function testCreatedAt()
    {
        self::assertEquals('1999-04-04', date('Y-m-d', $this->object->getCreatedAt()));
    }

    public function testHistory()
    {
        self::assertCount(3, $this->object->getHistory());
    }

    public function testWatchers()
    {
        self::assertCount(0, $this->object->getWatchers());

        $this->object->addWatcher($watcher = new Watcher());
        self::assertCount(1, $this->object->getWatchers());

        $this->object->removeWatcher($watcher);
        self::assertCount(0, $this->object->getWatchers());
    }

    public function testChildren()
    {
        self::assertCount(0, $this->object->getChildren());

        $this->object->addChild($child = new Child());
        self::assertCount(1, $this->object->getChildren());

        $this->object->removeChild($child);
        self::assertCount(0, $this->object->getChildren());
    }
}
