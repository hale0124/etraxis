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
use eTraxis\Tests\BaseTestCase;

class BaseMigrationTest extends BaseTestCase
{
    public function testVersion()
    {
        $expected  = '4.0.x';
        $migration = new BaseMigrationStub($this->doctrine->getConnection());

        $this->assertEquals($expected, $migration->getName());
    }

    public function testMysqlUpSuccess()
    {
        $schema    = new Schema();
        $migration = new MysqlMigrationStub($this->doctrine->getConnection());

        $this->expectOutputString('mysql up');
        $migration->up($schema);
    }

    public function testMysqlDownSuccess()
    {
        $schema    = new Schema();
        $migration = new MysqlMigrationStub($this->doctrine->getConnection());

        $this->expectOutputString('mysql down');
        $migration->down($schema);
    }

    /**
     * @expectedException \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function testMysqlUpFailure()
    {
        $schema    = new Schema();
        $migration = new BaseMigrationStub($this->doctrine->getConnection());

        $migration->up($schema);
    }

    /**
     * @expectedException \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function testMysqlDownFailure()
    {
        $schema    = new Schema();
        $migration = new BaseMigrationStub($this->doctrine->getConnection());

        $migration->down($schema);
    }

    public function testPostgresqlUpSuccess()
    {
        $schema    = new Schema();
        $migration = new PostgresqlMigrationStub($this->doctrine->getConnection());

        $this->expectOutputString('postgresql up');
        $migration->up($schema);
    }

    public function testPostgresqlDownSuccess()
    {
        $schema    = new Schema();
        $migration = new PostgresqlMigrationStub($this->doctrine->getConnection());

        $this->expectOutputString('postgresql down');
        $migration->down($schema);
    }

    /**
     * @expectedException \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function testPostgresqlUpFailure()
    {
        $schema    = new Schema();
        $migration = new BaseMigrationStub($this->doctrine->getConnection());

        $migration->up($schema);
    }

    /**
     * @expectedException \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function testPostgresqlDownFailure()
    {
        $schema    = new Schema();
        $migration = new BaseMigrationStub($this->doctrine->getConnection());

        $migration->down($schema);
    }

    public function testMssqlUpSuccess()
    {
        $schema    = new Schema();
        $migration = new MssqlMigrationStub($this->doctrine->getConnection());

        $this->expectOutputString('mssql up');
        $migration->up($schema);
    }

    public function testMssqlDownSuccess()
    {
        $schema    = new Schema();
        $migration = new MssqlMigrationStub($this->doctrine->getConnection());

        $this->expectOutputString('mssql down');
        $migration->down($schema);
    }

    /**
     * @expectedException \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function testMssqlUpFailure()
    {
        $schema    = new Schema();
        $migration = new BaseMigrationStub($this->doctrine->getConnection());

        $migration->up($schema);
    }

    /**
     * @expectedException \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function testMssqlDownFailure()
    {
        $schema    = new Schema();
        $migration = new BaseMigrationStub($this->doctrine->getConnection());

        $migration->down($schema);
    }

    public function testOracleUpSuccess()
    {
        $schema    = new Schema();
        $migration = new OracleMigrationStub($this->doctrine->getConnection());

        $this->expectOutputString('oracle up');
        $migration->up($schema);
    }

    public function testOracleDownSuccess()
    {
        $schema    = new Schema();
        $migration = new OracleMigrationStub($this->doctrine->getConnection());

        $this->expectOutputString('oracle down');
        $migration->down($schema);
    }

    /**
     * @expectedException \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function testOracleUpFailure()
    {
        $schema    = new Schema();
        $migration = new BaseMigrationStub($this->doctrine->getConnection());

        $migration->up($schema);
    }

    /**
     * @expectedException \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function testOracleDownFailure()
    {
        $schema    = new Schema();
        $migration = new BaseMigrationStub($this->doctrine->getConnection());

        $migration->down($schema);
    }
}
