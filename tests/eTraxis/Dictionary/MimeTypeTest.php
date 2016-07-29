<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Dictionary;

class MimeTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $expected = [
            'application/msword'                                                      => 'ms-word.png',
            'application/octet-stream'                                                => 'unknown.png',
            'application/pdf'                                                         => 'pdf.png',
            'application/vnd.ms-excel'                                                => 'ms-excel.png',
            'application/vnd.ms-excel.sheet.macroEnabled.12'                          => 'ms-excel.png',
            'application/vnd.oasis.opendocument.spreadsheet'                          => 'x-office-spreadsheet.png',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'       => 'x-office-spreadsheet.png',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'x-office-word.png',
            'application/x-rar-compressed'                                            => 'archive-rar.png',
            'application/x-zip-compressed'                                            => 'archive-zip.png',
            'image/bmp'                                                               => 'image-bmp.png',
            'image/gif'                                                               => 'image-gif.png',
            'image/jpeg'                                                              => 'image-jpeg.png',
            'image/png'                                                               => 'image-png.png',
            'text/html'                                                               => 'text-html.png',
            'text/plain'                                                              => 'text-plain.png',
            'text/xml'                                                                => 'text-xml.png',
            'video/mp4'                                                               => 'video.png',
            'video/x-ms-wmv'                                                          => 'video.png',
            'text/x-script.ksh'                                                       => 'text-script.png',
            'application/etraxis'                                                     => 'unknown.png',
            'audio/etraxis'                                                           => 'audio.png',
            'image/etraxis'                                                           => 'image.png',
            'message/etraxis'                                                         => 'message.png',
            'text/etraxis'                                                            => 'text-plain.png',
            'video/etraxis'                                                           => 'video.png',
        ];

        foreach ($expected as $mime => $file) {
            self::assertEquals($file, MimeType::get($mime));
        }
    }
}
