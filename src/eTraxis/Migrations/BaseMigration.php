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

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Base eTraxis migration.
 */
abstract class BaseMigration extends AbstractMigration
{
    /**
     * Returns version string for the migration.
     *
     * @return  string
     */
    abstract public function getVersion();

    /**
     * Returns custom migration name.
     *
     * @return  string
     */
    public function getName()
    {
        return $this->getVersion();
    }

    /**
     * Migrates the database up.
     *
     * @param   Schema $schema
     */
    public function up(Schema $schema)
    {
        $platform = $this->checkDatabasePlatform();
        $up       = $platform . 'Up';
        $this->$up($schema);
    }

    /**
     * Migrates the database down.
     *
     * @param   Schema $schema
     */
    public function down(Schema $schema)
    {
        $platform = $this->checkDatabasePlatform();
        $down     = $platform . 'Down';
        $this->$down($schema);
    }

    /**
     * Checks whether current database platform is supported.
     *
     * @throws  \Doctrine\DBAL\Migrations\AbortMigrationException
     *
     * @return  string Current database platform.
     */
    protected function checkDatabasePlatform()
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        switch ($platform) {

            case 'mysql':

                $this->abortIf(
                    !is_subclass_of($this, '\eTraxis\Migrations\MysqlMigrationInterface'),
                    'MySQL platform is not supported yet.'
                );

                break;

            case 'postgresql':

                $this->abortIf(
                    !is_subclass_of($this, '\eTraxis\Migrations\PostgresqlMigrationInterface'),
                    'PostgreSQL platform is not supported yet.'
                );

                break;

            case 'mssql':

                $this->abortIf(
                    !is_subclass_of($this, '\eTraxis\Migrations\MssqlMigrationInterface'),
                    'Microsoft SQL Server platform is not supported yet.'
                );

                break;

            case 'oracle':

                $this->abortIf(
                    !is_subclass_of($this, '\eTraxis\Migrations\OracleMigrationInterface'),
                    'Oracle platform is not supported yet.'
                );

                break;

            default:

                $this->abortIf(true, "Unknown database platform: {$platform}.");
        }

        return $platform;
    }
}
