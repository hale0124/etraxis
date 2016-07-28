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

use Dictionary\StaticDictionary;

/**
 * Field permissions.
 */
class FieldPermission extends StaticDictionary
{
    const FALLBACK = self::NONE;

    const NONE       = 'none';
    const READ_ONLY  = 'read';
    const READ_WRITE = 'write';

    // Aliases.
    const READ  = self::READ_ONLY;
    const WRITE = self::READ_WRITE;

    protected static $dictionary = [
        self::NONE       => 'field.permissions.none',
        self::READ_ONLY  => 'field.permissions.read_only',
        self::READ_WRITE => 'field.permissions.read_write',
    ];
}
