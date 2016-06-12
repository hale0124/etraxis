<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
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
use eTraxis\Dictionary\DatabasePlatform;

/**
 * Implements custom "INTDIV" function which returns the integer quotient of the division.
 * This is an SQL analogue of PHP http://php.net/intdiv function.
 *
 * For example:
 *   INTDIV(7, 3) = 2
 */
class IntDivFunction extends FunctionNode
{
    /** @var \Doctrine\ORM\Query\AST\InputParameter */
    protected $dividend;

    /** @var \Doctrine\ORM\Query\AST\InputParameter */
    protected $divisor;

    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $sql = [
            DatabasePlatform::MYSQL      => '(%s DIV %s)',
            DatabasePlatform::POSTGRESQL => '(%s / %s)',
        ];

        $platform = $sqlWalker->getConnection()->getDatabasePlatform()->getName();

        return sprintf($sql[$platform],
            $this->dividend->dispatch($sqlWalker),
            $this->divisor->dispatch($sqlWalker)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->dividend = $parser->ArithmeticPrimary();

        $parser->match(Lexer::T_COMMA);

        $this->divisor = $parser->ArithmeticPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
