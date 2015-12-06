<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Migrations;

use Doctrine\DBAL\Schema\Schema;

class PostgresqlMigrationStub extends BaseMigrationStub implements PostgresqlMigrationInterface
{
    public function __construct()
    {
        $this->connection = new ConnectionStub('postgresql');
    }

    public function postgresqlUp(Schema $schema)
    {
        echo 'postgresql up';
    }

    public function postgresqlDown(Schema $schema)
    {
        echo 'postgresql down';
    }
}
