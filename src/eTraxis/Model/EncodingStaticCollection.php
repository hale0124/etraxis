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

namespace eTraxis\Model;

/**
 * Static collection of encodings.
 */
class EncodingStaticCollection extends AbstractStaticCollection
{
    /**
     * {@inheritdoc}
     */
    public static function getCollection()
    {
        return [
            'ISO-8859-6'   => 'Arabic (ISO-8859-6)',
            'ArmSCII-8'    => 'Armenian (ArmSCII-8)',
            'ISO-8859-4'   => 'Baltic (ISO-8859-4)',
            'ISO-8859-13'  => 'Baltic (ISO-8859-13)',
            'ISO-8859-14'  => 'Celtic (ISO-8859-14)',
            'ISO-8859-2'   => 'Central European (ISO-8859-2)',
            'EUC-CN'       => 'Chinese Simplified (EUC-CN)',
            'GB18030'      => 'Chinese Simplified (GB18030)',
            'BIG-5'        => 'Chinese Traditional (Big5)',
            'EUC-TW'       => 'Chinese Traditional (EUC-TW)',
            'ISO-8859-5'   => 'Cyrillic (ISO-8859-5)',
            'KOI8-R'       => 'Cyrillic (KOI8-R)',
            'KOI8-U'       => 'Cyrillic (KOI8-U)',
            'Windows-1251' => 'Cyrillic (Windows-1251)',
            'ISO-8859-7'   => 'Greek (ISO-8859-7)',
            'ISO-8859-8'   => 'Hebrew (ISO-8859-8)',
            'EUC-JP'       => 'Japanese (EUC-JP)',
            'ISO-2022-JP'  => 'Japanese (ISO-2022-JP)',
            'JIS'          => 'Japanese (JIS)',
            'SJIS'         => 'Japanese (Shift JIS)',
            'EUC-KR'       => 'Korean (EUC-KR)',
            'ISO-2022-KR'  => 'Korean (ISO-2022-KR)',
            'ISO-8859-10'  => 'Nordic (ISO-8859-10)',
            'ISO-8859-16'  => 'Romanian (ISO-8859-16)',
            'ISO-8859-3'   => 'South European (ISO-8859-3)',
            'ISO-8859-9'   => 'Turkish (ISO-8859-9)',
            'Windows-1254' => 'Turkish (Windows-1254)',
            'UCS-2'        => 'Unicode (UCS-2)',
            'UCS-4'        => 'Unicode (UCS-4)',
            'UTF-8'        => 'Unicode (UTF-8)',
            'UTF-16'       => 'Unicode (UTF-16)',
            'ISO-8859-1'   => 'Western European (ISO-8859-1)',
            'ISO-8859-15'  => 'Western European (ISO-8859-15)',
            'Windows-1252' => 'Western European (Windows-1252)',
        ];
    }
}
