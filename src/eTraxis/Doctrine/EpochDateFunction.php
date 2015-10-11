<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use eTraxis\Collection\DatabasePlatform;

/**
 * Implements custom "EPOCH_DATE" function which retrieves ISO 8601 date from specified Unix timestamp.
 *
 * For example:
 *   EPOCH_DATE(922536000) = '1999-03-28'
 */
class EpochDateFunction extends FunctionNode
{
    /** @var \Doctrine\ORM\Query\AST\InputParameter */
    protected $parameter = null;

    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $sql = [
            DatabasePlatform::MYSQL      => 'DATE(FROM_UNIXTIME(%s))',
            DatabasePlatform::POSTGRESQL => "TO_CHAR(TO_TIMESTAMP(%s), 'YYYY-MM-DD')",
            DatabasePlatform::MSSQL      => "CONVERT(DATE, DATEADD(SECOND, %s, '19700101'))",
            DatabasePlatform::ORACLE     => "TO_CHAR(TO_DATE('1970-01-01', 'YYYY-MM-DD') + NUMTODSINTERVAL(%s, 'SECOND'), 'YYYY-MM-DD')",
        ];

        $platform = $sqlWalker->getConnection()->getDatabasePlatform()->getName();

        return sprintf($sql[$platform], $this->parameter->dispatch($sqlWalker));
    }

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->parameter = $parser->StringPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
