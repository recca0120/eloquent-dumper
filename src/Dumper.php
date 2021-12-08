<?php

namespace Recca0120\EloquentDumper;

use PDO;
use PhpMyAdmin\SqlParser\Utils\Formatter;
use Recca0120\EloquentDumper\Grammars\Grammar;
use Recca0120\EloquentDumper\Grammars\PdoGrammar;

class Dumper
{
    public const DEFAULT = 'default';
    public const PDO = 'pdo';
    public const MYSQL = 'mysql';
    public const SQLITE = 'sqlite';
    public const POSTGRES = 'postgres';
    public const PGSQL = 'pgsql';
    public const SQLSERVER = 'sqlserver';
    public const SQLSRV = 'sqlsrv';
    public const MSSQL = 'mssql';
    public const NONE = 'none';

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
     * @return Dumper
     */
    public function setPdo(PDO $pdo): self
    {
        PdoGrammar::setPdo($pdo);

        return $this;
    }

    /**
     * @param string $grammar
     * @return Dumper
     */
    public function setGrammar(string $grammar): self
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
