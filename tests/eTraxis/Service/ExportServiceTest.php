<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service;

use eTraxis\Collection\CsvDelimiter;
use eTraxis\Collection\LineEnding;
use eTraxis\Tests\BaseTestCase;

class ExportServiceTest extends BaseTestCase
{
    public function test1()
    {
        $data = [
            ['euclid', 'Euclid', null],
            ['euler', 'Leonhard Euler', 1707],
            ['gauss', 'Carl Friedrich Gauss', 1777],
            ['riemann', 'Bernhard Riemann', 1826],
        ];

        $expected = [
            'euclid,Euclid,',
            'euler,Leonhard Euler,1707',
            'gauss,Carl Friedrich Gauss,1777',
            'riemann,Bernhard Riemann,1826',
            null,
        ];

        $this->expectOutputString(implode("\n", $expected));

        $query = new ExportCsvQuery([
            'filename'  => 'test',
            'delimiter' => CsvDelimiter::COMMA,
            'encoding'  => 'UTF-8',
            'tail'      => LineEnding::UNIX,
        ]);

        $service = new ExportService();

        /** @var \Symfony\Component\HttpFoundation\StreamedResponse $request */
        $request = $service->exportCsv($query, $data);

        $this->assertEquals('UTF-8', $request->getCharset());

        $this->assertContains('test.csv', $request->headers->get('content-disposition'));

        $request->sendContent();
    }

    public function test2()
    {
        $data = [
            ['euclid', 'Euclid', null],
            ['euler', 'Leonhard Euler', 1707],
            ['gauss', 'Carl Friedrich Gauss', 1777],
            ['riemann', 'Bernhard Riemann', 1826],
        ];

        $expected = [
            'euclid Euclid ',
            'euler "Leonhard Euler" 1707',
            'gauss "Carl Friedrich Gauss" 1777',
            'riemann "Bernhard Riemann" 1826',
            null,
        ];

        $this->expectOutputString(implode("\r\n", $expected));

        $query = new ExportCsvQuery([
            'filename'  => '.csv',
            'delimiter' => CsvDelimiter::SPACE,
            'encoding'  => 'Windows-1251',
            'tail'      => LineEnding::WINDOWS,
        ]);

        $service = new ExportService();

        /** @var \Symfony\Component\HttpFoundation\StreamedResponse $request */
        $request = $service->exportCsv($query, $data);

        $this->assertEquals('Windows-1251', $request->getCharset());

        $this->assertContains('eTraxis.csv', $request->headers->get('content-disposition'));

        $request->sendContent();
    }
}
