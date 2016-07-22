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

use eTraxis\Dictionary\EventType;
use eTraxis\Tests\TransactionalTestCase;
use eTraxis\Traits\ReflectionTrait;

class CommentTest extends TransactionalTestCase
{
    use ReflectionTrait;

    /** @var Comment */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => '200 feet of hanging rope for the hanging of multiheaded monster.',
        ]);

        $comments = $record->getComments();

        $this->object = reset($comments);
    }

    public function testConstruct()
    {
        $expected = 'Test comment';

        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => '200 feet of hanging rope for the hanging of multiheaded monster.',
        ]);

        $user = $this->findUser('hubert');

        $comment = new Comment($record, $user, $expected, true);

        self::assertEquals($record, $comment->getEvent()->getRecord());
        self::assertEquals($user, $comment->getEvent()->getUser());
        self::assertEquals(EventType::PRIVATE_COMMENT, $comment->getEvent()->getType());
        self::assertEquals($expected, $comment->getText());
        self::assertTrue($comment->isPrivate());
    }

    public function testId()
    {
        $expected = random_int(1, PHP_INT_MAX);
        $this->setProperty($this->object, 'id', $expected);
        self::assertEquals($expected, $this->object->getId());
    }

    public function testEvent()
    {
        $expected = EventType::PUBLIC_COMMENT;
        self::assertEquals($expected, $this->object->getEvent()->getType());
    }

    public function testText()
    {
        $expected = 'Good news, everyone!';
        self::assertEquals($expected, substr($this->object->getText(), 0, strlen($expected)));
    }

    public function testIsPrivate()
    {
        self::assertFalse($this->object->isPrivate());
    }
}
