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

class AttachmentTest extends TransactionalTestCase
{
    use ReflectionTrait;

    /** @var Attachment */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject'  => 'Autoloading Standard',
            'closedAt' => null,
        ]);

        $attachments = $record->getAttachments();

        $this->object = reset($attachments);
    }

    public function testConstruct()
    {
        $name = 'My file.pdf';
        $size = random_int(1, 1000);
        $type = 'application/pdf';

        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'Prizes for the claw crane',
        ]);

        $user = $this->findUser('hubert');

        $attachment = new Attachment($record, $user, $name, $size, $type);

        self::assertEquals($name, $attachment->getName());
        self::assertEquals($size, $attachment->getSize());
        self::assertEquals($type, $attachment->getType());
        self::assertRegExp('/^([[:xdigit:]]{32})$/is', $this->object->getUuid());
        self::assertFalse($attachment->isDeleted());
    }

    public function testId()
    {
        $expected = random_int(1, PHP_INT_MAX);
        $this->setProperty($this->object, 'id', $expected);
        self::assertEquals($expected, $this->object->getId());
    }

    public function testEvent()
    {
        $expected = EventType::FILE_ATTACHED;
        self::assertEquals($expected, $this->object->getEvent()->getType());
    }

    public function testRecord()
    {
        $expected = 'Autoloading Standard';
        self::assertEquals($expected, $this->object->getRecord()->getSubject());
    }

    public function testName()
    {
        $expected = 'example.php';
        self::assertEquals($expected, $this->object->getName());
    }

    public function testSize()
    {
        $expected = 5891;
        self::assertEquals($expected, $this->object->getSize());
    }

    public function testType()
    {
        $expected = 'application/x-httpd-php-source';
        self::assertEquals($expected, $this->object->getType());
    }

    public function testMimeImage()
    {
        $expected = 'text-php.png';
        self::assertEquals($expected, $this->object->getMimeImage());
    }

    public function testUuid()
    {
        $expected = '/^([[:xdigit:]]{32})$/is';
        self::assertRegExp($expected, $this->object->getUuid());
    }

    public function testIsDeleted()
    {
        $this->object->setDeleted(true);
        self::assertTrue($this->object->isDeleted());

        $this->object->setDeleted(false);
        self::assertFalse($this->object->isDeleted());
    }

    public function testGetAbsolutePath()
    {
        $expected = getcwd() . '/var/' . $this->object->getUuid();

        file_put_contents($expected, null);
        self::assertEquals($expected, $this->object->getAbsolutePath('./var/'));
        unlink($expected);
    }
}
