<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
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
