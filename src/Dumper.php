<?php

namespace Recca0120\EloquentDumper;

use DateTime;
use PDO;
use PhpMyAdmin\SqlParser\Utils\Formatter;
use Recca0120\EloquentDumper\Grammars\Grammar;
use Recca0120\EloquentDumper\Grammars\PdoGrammar;

class Dumper
{
    const NONE = 'none';
    const PDO = 'pdo';
    const MYSQL = 'mysql';
    const SQLITE = 'sqlite';
    const POSTGRES = 'postgres';
    const PGSQL = 'pgsql';
    const SQLSERVER = 'sqlserver';
    const SQLSRV = 'sqlsrv';
    const MSSQL = 'mssql';

    /**
     * @var Grammar
     */
    private $grammar;

    /**
     * Dumper constructor.
     * @param string $grammar
     */
    public function __construct($grammar = self::PDO)
    {
        $this->setGrammar($grammar);
    }

    /**
     * @param PDO $pdo
     * @return $this
     */
    public function setPdo(PDO $pdo)
    {
        PdoGrammar::setPdo($pdo);

        return $this;
    }

    /**
     * @param string $grammar
     * @return $this
     */
    public function setGrammar($grammar)
    {
        $this->grammar = Grammar::factory($grammar);

        return $this;
    }

    /**
     * @param string $sql
     * @param array $bindings
     * @return string
     */
    public function dump($sql, $bindings)
    {
        return $this->format(vsprintf($this->toSql($sql), $this->toBindings($bindings)));
    }

    /**
     * @param string $sql
     * @return string
     */
    protected function format($sql)
    {
        return Formatter::format($sql);
    }

    /**
     * @param string $sql
     * @return string
     */
    private function toSql($sql)
    {
        return str_replace(['%', '?'], ['%%', '%s'], $this->grammar->columnize($sql));
    }

    /**
     * @param array $bindings
     * @return array|string[]
     */
    private function toBindings($bindings)
    {
        return array_map(function ($binding) {
            if (is_array($binding)) {
                $binding = implode(', ', array_map(function ($value) {
                    return is_string($value) === true ? $this->grammar->parameterize($value) : $value;
                }, $binding));
            }

            if ($binding instanceof DateTime) {
                return $this->grammar->parameterize($binding->format('Y-m-d H:i:s'));
            }

            if (is_string($binding) || is_object($binding) && method_exists($binding, '__toString')) {
                return $this->grammar->parameterize((string) $binding);
            }

            if (is_bool($binding)) {
                return $binding ? 1 : 0;
            }

            if ($binding === null) {
                return 'NULL';
            }

            return $binding;
        }, $bindings);
    }
}
