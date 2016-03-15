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

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use eTraxis\Collection\DatabasePlatform;

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
     * {@inheritdoc}
     */
    final public function getDescription()
    {
        return $this->getVersion();
    }

    /**
     * {@inheritdoc}
     */
    final public function up(Schema $schema)
    {
        $platform = $this->checkDatabasePlatform();
        $up       = $platform . 'Up';
        $this->$up($schema);
    }

    /**
     * {@inheritdoc}
     */
    final public function down(Schema $schema)
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

            case DatabasePlatform::MYSQL:

                $this->abortIf(
                    !($this instanceof MysqlMigrationInterface),
                    'MySQL platform is not supported yet.'
                );

                break;

            case DatabasePlatform::POSTGRESQL:

                $this->abortIf(
                    !($this instanceof PostgresqlMigrationInterface),
                    'PostgreSQL platform is not supported yet.'
                );

                break;

            case DatabasePlatform::MSSQL:

                $this->abortIf(
                    !($this instanceof MssqlMigrationInterface),
                    'Microsoft SQL Server platform is not supported yet.'
                );

                break;

            case DatabasePlatform::ORACLE:

                $this->abortIf(
                    !($this instanceof OracleMigrationInterface),
                    'Oracle platform is not supported yet.'
                );

                break;

            default:

                $this->abortIf(true, "Unknown database platform: {$platform}.");
        }

        return $platform;
    }
}
