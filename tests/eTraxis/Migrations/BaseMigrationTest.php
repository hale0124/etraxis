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

class BaseMigrationTest extends \PHPUnit_Framework_TestCase
{
    public function testVersion()
    {
        $expected  = '4.0.x';
        $migration = new BaseMigrationStub();

        self::assertEquals($expected, $migration->getDescription());
    }

    public function testMysqlUpSuccess()
    {
        $schema    = new Schema();
        $migration = new MysqlMigrationStub();

        $this->expectOutputString('mysql up');
        $migration->up($schema);
    }

    public function testMysqlDownSuccess()
    {
        $schema    = new Schema();
        $migration = new MysqlMigrationStub();

        $this->expectOutputString('mysql down');
        $migration->down($schema);
    }

    /**
     * @expectedException \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function testMysqlUpFailure()
    {
        $schema    = new Schema();
        $migration = new BaseMigrationStub();

        $migration->up($schema);
    }

    /**
     * @expectedException \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function testMysqlDownFailure()
    {
        $schema    = new Schema();
        $migration = new BaseMigrationStub();

        $migration->down($schema);
    }

    public function testPostgresqlUpSuccess()
    {
        $schema    = new Schema();
        $migration = new PostgresqlMigrationStub();

        $this->expectOutputString('postgresql up');
        $migration->up($schema);
    }

    public function testPostgresqlDownSuccess()
    {
        $schema    = new Schema();
        $migration = new PostgresqlMigrationStub();

        $this->expectOutputString('postgresql down');
        $migration->down($schema);
    }

    /**
     * @expectedException \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function testPostgresqlUpFailure()
    {
        $schema    = new Schema();
        $migration = new BaseMigrationStub();

        $migration->up($schema);
    }

    /**
     * @expectedException \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function testPostgresqlDownFailure()
    {
        $schema    = new Schema();
        $migration = new BaseMigrationStub();

        $migration->down($schema);
    }
}
