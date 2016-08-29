<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Attachments;

use eTraxis\Entity\Attachment;
use eTraxis\Entity\Event;
use eTraxis\Entity\Record;
use eTraxis\Tests\TransactionalTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AttachFileCommandTest extends TransactionalTestCase
{
    /** @var Record */
    private $record;

    /** @var UploadedFile */
    private $file;

    protected function setUp()
    {
        parent::setUp();

        /** @var Record $recod */
        $this->record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        $filename = getcwd() . '/var/_' . md5('test.txt');

        file_put_contents($filename, str_repeat('*', 1024));

        $this->file = new UploadedFile($filename, 'test.txt', 'text/plain', filesize($filename), null, true);
    }

    protected function tearDown()
    {
        $filename = getcwd() . '/var/_' . md5('test.txt');

        if (file_exists($filename)) {
            unlink($filename);
        }

        parent::tearDown();
    }

    public function testSuccess()
    {
        $this->loginAs('hubert');

        $events = count($this->doctrine->getRepository(Event::class)->findAll());

        $command = new AttachFileCommand([
            'record' => $this->record->getId(),
            'file'   => $this->file,
        ]);

        $this->commandbus->handle($command);

        /** @var Attachment $attachment */
        $attachment = $this->doctrine->getRepository(Attachment::class)->findOneBy([
            'name' => 'test.txt',
        ]);

        self::assertNotNull($attachment);
        self::assertCount($events + 1, $this->doctrine->getRepository(Event::class)->findAll());
        self::assertFileExists($attachment->getAbsolutePath('var'));

        unlink($attachment->getAbsolutePath('var'));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage This file cannot be uploaded because it exceeds the maximum allowed file size (1 KB).
     */
    public function testMaxSize()
    {
        $filename = getcwd() . '/var/_' . md5('huge.txt');

        file_put_contents($filename, str_repeat('*', 1025));

        $file = new UploadedFile($filename, 'huge.txt', 'text/plain', filesize($filename), null, true);

        $this->loginAs('hubert');

        $command = new AttachFileCommand([
            'record' => $this->record->getId(),
            'file'   => $file,
        ]);

        try {
            $this->commandbus->handle($command);
        }
        catch (BadRequestHttpException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        finally {
            unlink($filename);
        }

        self::fail('Failed asserting that exception of type "\Symfony\Component\HttpKernel\Exception\BadRequestHttpException" is thrown.');
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown user.
     */
    public function testUnknownUser()
    {
        $command = new AttachFileCommand([
            'record' => $this->record->getId(),
            'file'   => $this->file,
        ]);

        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown record.
     */
    public function testNotFound()
    {
        $this->loginAs('hubert');

        $command = new AttachFileCommand([
            'record' => self::UNKNOWN_ENTITY_ID,
            'file'   => $this->file,
        ]);

        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testAccessDenied()
    {
        $this->loginAs('fry');

        $command = new AttachFileCommand([
            'record' => $this->record->getId(),
            'file'   => $this->file,
        ]);

        $this->commandbus->handle($command);
    }
}
