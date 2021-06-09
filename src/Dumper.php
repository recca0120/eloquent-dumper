<?php

namespace Recca0120\EloquentDumper;

use PDO;
use PhpMyAdmin\SqlParser\Utils\Formatter;
use Recca0120\EloquentDumper\Grammars\Grammar;
use Recca0120\EloquentDumper\Grammars\PdoGrammar;

class Dumper
{
    public const NONE = 'none';
    public const PDO = 'pdo';
    public const MYSQL = 'mysql';
    public const SQLITE = 'sqlite';
    public const POSTGRES = 'postgres';
    public const PGSQL = 'pgsql';
    public const SQLSERVER = 'sqlserver';
    public const SQLSRV = 'sqlsrv';
    public const MSSQL = 'mssql';

    /**
     * @var Grammar
     */
    private $grammar;
    /**
     * @var Converter
     */
    private $converter;

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
        $this->converter = new Converter($this->grammar);

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
        return array_map([$this->converter, 'handle'], $bindings);
    }
}
