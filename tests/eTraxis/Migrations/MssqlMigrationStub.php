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

class MssqlMigrationStub extends BaseMigrationStub implements MssqlMigrationInterface
{
    public function __construct()
    {
        $this->connection = new ConnectionStub('mssql');
    }

    public function mssqlUp(Schema $schema)
    {
        print('mssql up');
    }

    public function mssqlDown(Schema $schema)
    {
        print('mssql down');
    }
}
