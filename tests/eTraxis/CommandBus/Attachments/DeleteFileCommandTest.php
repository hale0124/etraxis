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
use eTraxis\Tests\TransactionalTestCase;

class DeleteFileCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        $this->loginAs('pmjones');

        /** @var Attachment $attachment */
        $attachment = $this->doctrine->getRepository(Attachment::class)->findOneBy([
            'name' => 'example.php',
        ]);

        $filename = getcwd() . '/var/' . $attachment->getUuid();

        file_put_contents($filename, null);

        self::assertFileExists($filename);
        self::assertFalse($attachment->isDeleted());

        $events = count($this->doctrine->getRepository(Event::class)->findAll());

        $command = new DeleteFileCommand(['id' => $attachment->getId()]);
        $this->commandbus->handle($command);

        /** @var Attachment $attachment */
        $attachment = $this->doctrine->getRepository(Attachment::class)->findOneBy([
            'name' => 'example.php',
        ]);

        self::assertFileNotExists($filename);
        self::assertTrue($attachment->isDeleted());
        self::assertCount($events + 1, $this->doctrine->getRepository(Event::class)->findAll());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown user.
     */
    public function testUnknownUser()
    {
        /** @var Attachment $attachment */
        $attachment = $this->doctrine->getRepository(Attachment::class)->findOneBy([
            'name' => 'example.php',
        ]);

        $command = new DeleteFileCommand(['id' => $attachment->getId()]);
        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown file.
     */
    public function testNotFound()
    {
        $this->loginAs('pmjones');

        $command = new DeleteFileCommand(['id' => self::UNKNOWN_ENTITY_ID]);
        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown file.
     */
    public function testAlreadyDeleted()
    {
        $this->loginAs('pmjones');

        /** @var Attachment $attachment */
        $attachment = $this->doctrine->getRepository(Attachment::class)->findOneBy([
            'name' => 'Meta Document.pdf',
        ]);

        $command = new DeleteFileCommand(['id' => $attachment->getId()]);
        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testAccessDenied()
    {
        $this->loginAs('hubert');

        /** @var Attachment $attachment */
        $attachment = $this->doctrine->getRepository(Attachment::class)->findOneBy([
            'name' => 'example.php',
        ]);

        $command = new DeleteFileCommand(['id' => $attachment->getId()]);
        $this->commandbus->handle($command);
    }
}
