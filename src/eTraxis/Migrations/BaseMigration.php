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
use eTraxis\Dictionary\DatabasePlatform;

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
     * Checks whether current database platform is MySQL.
     *
     * @return  bool
     */
    public function isMysql()
    {
        return DatabasePlatform::MYSQL === $this->connection->getDatabasePlatform()->getName();
    }

    /**
     * Checks whether current database platform is PostgreSQL.
     *
     * @return  bool
     */
    public function isPostgresql()
    {
        return DatabasePlatform::POSTGRESQL === $this->connection->getDatabasePlatform()->getName();
    }

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
    public function preUp(Schema $schema)
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        $this->abortIf(
            !DatabasePlatform::has($platform),
            'Unsupported database platform - ' . $platform
        );
    }

    /**
     * {@inheritdoc}
     */
    public function preDown(Schema $schema)
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        $this->abortIf(
            !DatabasePlatform::has($platform),
            'Unsupported database platform - ' . $platform
        );
    }
}
