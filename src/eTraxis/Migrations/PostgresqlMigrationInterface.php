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
 * PostgreSQL migration.
 */
interface PostgresqlMigrationInterface
{
    /**
     * Migrates the database up.
     *
     * @param   Schema $schema
     */
    public function postgresqlUp(Schema $schema);

    /**
     * Migrates the database down.
     *
     * @param   Schema $schema
     */
    public function postgresqlDown(Schema $schema);
}
