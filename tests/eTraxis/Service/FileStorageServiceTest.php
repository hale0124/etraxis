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

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FileStorageServiceTest extends KernelTestCase
{
    protected function setUp()
    {
        self::bootKernel();
    }

    public function testGetAbsolutePath()
    {
        $expected = getcwd() . '/var/files/.gitkeep';

        $service = new FileStorage\FileStorageService('./var/files/');

        self::assertEquals($expected, $service->getAbsolutePath('/.gitkeep'));
    }
}
