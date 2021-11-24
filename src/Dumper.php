<?php

namespace Recca0120\EloquentDumper;

use PDO;
use PhpMyAdmin\SqlParser\Utils\Formatter;
use Recca0120\EloquentDumper\Grammars\Grammar;
use Recca0120\EloquentDumper\Grammars\PdoGrammar;

class Dumper
{
    const DEFAULT = 'default';
    const PDO = 'pdo';
    const MYSQL = 'mysql';
    const SQLITE = 'sqlite';
    const POSTGRES = 'postgres';
    const PGSQL = 'pgsql';
    const SQLSERVER = 'sqlserver';
    const SQLSRV = 'sqlsrv';
    const MSSQL = 'mssql';
    const NONE = 'none';

    /**
     * @var Grammar|null
     */
    private $grammar;
    /**
     * @var Converter|null
     */
    private $converter;

    /**
     * Dumper constructor.
     * @param string $grammar
     */
    public function __construct(string $grammar = self::PDO)
    {
        $this->setGrammar($grammar);
    }

    /**
     * @param PDO $pdo
     * @return $this
     */
    public function setPdo(PDO $pdo): Dumper
    {
        PdoGrammar::setPdo($pdo);

        return $this;
    }

    /**
     * @param string $grammar
     * @return $this
     */
    public function setGrammar(string $grammar): Dumper
    {
        $this->grammar = Grammar::factory($grammar);
        $this->converter = new Converter($this->grammar);

        return $this;
    }

    /**
     * @param string $sql
     * @param array $bindings
     * @param bool $format
     * @return string
     */
    public function dump(string $sql, array $bindings, bool $format = true): string
    {
        $raw = $this->bindValues($sql, $bindings);

        return $format ? $this->format($raw) : $raw;
    }

    /**
     * @param string $sql
     * @return string
     */
    private function format(string $sql): string
    {
        return Formatter::format($sql);
    }

    /**
     * @param string $sql
     * @param array $bindings
     * @return string
     */
    private function bindValues(string $sql, array $bindings): string
    {
        return vsprintf(
            str_replace(['%', '?'], ['%%', '%s'], $this->grammar->columnize($sql)),
            array_map([$this->converter, 'handle'], $bindings)
        );
    }
}
