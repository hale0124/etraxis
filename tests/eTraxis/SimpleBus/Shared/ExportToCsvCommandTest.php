<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Shared;

use eTraxis\Collection\CsvDelimiter;
use eTraxis\Collection\LineEnding;
use eTraxis\Tests\BaseTestCase;

class ExportToCsvCommandTest extends BaseTestCase
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

        $command = new ExportToCsvCommand([
            'filename'  => 'test',
            'delimiter' => CsvDelimiter::COMMA,
            'encoding'  => 'UTF-8',
            'tail'      => LineEnding::UNIX,
            'data'      => $data,
        ]);

        $this->command_bus->handle($command);

        /** @var \Symfony\Component\HttpFoundation\StreamedResponse $request */
        $request = $command->result;

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

        $command = new ExportToCsvCommand([
            'filename'  => '.csv',
            'delimiter' => CsvDelimiter::SPACE,
            'encoding'  => 'Windows-1251',
            'tail'      => LineEnding::WINDOWS,
            'data'      => $data,
        ]);

        $this->command_bus->handle($command);

        /** @var \Symfony\Component\HttpFoundation\StreamedResponse $request */
        $request = $command->result;

        $this->assertEquals('Windows-1251', $request->getCharset());

        $this->assertContains('eTraxis.csv', $request->headers->get('content-disposition'));

        $request->sendContent();
    }
}
