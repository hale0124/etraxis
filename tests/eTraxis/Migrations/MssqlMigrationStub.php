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

class MssqlMigrationStub extends BaseMigrationStub implements MssqlMigrationInterface
{
    public function __construct()
    {
        parent::__construct('mssql');
    }

    public function mssqlUp(Schema $schema)
    {
        echo 'mssql up';
    }

    public function mssqlDown(Schema $schema)
    {
        echo 'mssql down';
    }
}
