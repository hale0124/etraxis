<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Microsoft SQL Server migration.
 */
interface MssqlMigrationInterface
{
    /**
     * Migrates the database up.
     *
     * @param   Schema $schema
     */
    public function mssqlUp(Schema $schema);

    /**
     * Migrates the database down.
     *
     * @param   Schema $schema
     */
    public function mssqlDown(Schema $schema);
}
