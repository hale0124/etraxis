<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Dictionary\Legacy;

use Dictionary\StaticDictionary;

/**
 * Legacy field types to be converted from 3.9.x to 4.0.0.
 */
class FieldType extends StaticDictionary
{
    protected static $dictionary = [
        1 => 'number',
        2 => 'string',
        3 => 'text',
        4 => 'checkbox',
        5 => 'list',
        6 => 'record',
        7 => 'date',
        8 => 'duration',
        9 => 'decimal',
    ];
}
