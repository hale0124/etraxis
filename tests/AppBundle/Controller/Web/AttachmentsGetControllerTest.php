<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\Controller\Web;

use eTraxis\Entity\Attachment;
use eTraxis\Tests\ControllerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AttachmentsGetControllerTest extends ControllerTestCase
{
    public function testDownloadAction()
    {
        /** @var \Symfony\Bridge\Doctrine\RegistryInterface $doctrine */
        $doctrine = $this->client->getContainer()->get('doctrine');

        /** @var Attachment $existing */
        $existing = $doctrine->getRepository(Attachment::class)->findOneBy([
            'name' => 'example.php',
        ]);

        /** @var Attachment $deleted */
        $deleted = $doctrine->getRepository(Attachment::class)->findOneBy([
            'name' => 'Meta Document.pdf',
        ]);

        file_put_contents(getcwd() . '/var/' . $existing->getId(), null);

        $uri = $this->router->generate('web_download_attachment', [
            'id' => $existing->getId(),
        ]);

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertLoginPage();

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('mwop');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_OK);

        $uri = $this->router->generate('web_download_attachment', [
            'id' => $deleted->getId(),
        ]);

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_NOT_FOUND);

        unlink(getcwd() . '/var/' . $existing->getId());
    }
}
