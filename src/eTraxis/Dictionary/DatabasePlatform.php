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
 * Static collection of supported database platforms.
 */
class DatabasePlatform extends StaticDictionary
{
    const MYSQL      = 'mysql';
    const POSTGRESQL = 'postgresql';
    const MSSQL      = 'mssql';
    const ORACLE     = 'oracle';

    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            self::MYSQL      => 'MySQL',
            self::POSTGRESQL => 'PostgreSQL',
            self::MSSQL      => 'Microsoft SQL Server',
            self::ORACLE     => 'Oracle',
        ];
    }
}
