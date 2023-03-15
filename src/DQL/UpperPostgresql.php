<?php

namespace App\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Clase de función para habilitar UPPER en postgresql
 * @author Juani Alarcón <jialarcon@justiciasantafe.gov.ar>
 */
class UpperPostgresql extends FunctionNode
{

    public $value;

    public function parse(Parser $parser): void 
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->value = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'UPPER(' . $this->value->dispatch($sqlWalker) . '::varchar)';
    }
}
