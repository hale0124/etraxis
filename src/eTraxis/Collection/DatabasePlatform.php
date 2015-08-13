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

namespace eTraxis\Collection;

/**
 * Static collection of supported database platforms.
 */
class DatabasePlatform extends AbstractStaticCollection
{
    const MYSQL      = 'mysql';
    const POSTGRESQL = 'postgresql';
    const MSSQL      = 'mssql';
    const ORACLE     = 'oracle';

    /**
     * {@inheritdoc}
     */
    public static function getCollection()
    {
        return [
            self::MYSQL      => 'MySQL',
            self::POSTGRESQL => 'PostgreSQL',
            self::MSSQL      => 'Microsoft SQL Server',
            self::ORACLE     => 'Oracle',
        ];
    }
}
