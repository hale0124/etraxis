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
 * Supported database platforms.
 */
class DatabasePlatform extends StaticDictionary
{
    const MYSQL      = 'mysql';
    const POSTGRESQL = 'postgresql';

    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            self::MYSQL      => 'MySQL',
            self::POSTGRESQL => 'PostgreSQL',
        ];
    }
}
