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

class OracleMigrationStub extends BaseMigrationStub implements OracleMigrationInterface
{
    public function __construct()
    {
        $this->connection = new ConnectionStub('oracle');
    }

    public function oracleUp(Schema $schema)
    {
        print('oracle up');
    }

    public function oracleDown(Schema $schema)
    {
        print('oracle down');
    }
}
