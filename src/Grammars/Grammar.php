<?php

namespace Recca0120\EloquentDumper\Grammars;

use PDO;

abstract class Grammar
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

    private static $drivers = [
        self::DEFAULT => PdoGrammar::class,
        self::PDO => PdoGrammar::class,
        self::MYSQL => MySqlGrammar::class,
        self::SQLITE => SQLiteGrammar::class,
        self::POSTGRES => PostgresGrammar::class,
        self::PGSQL => PostgresGrammar::class,
        self::SQLSERVER => SqlServerGrammar::class,
        self::SQLSRV => SqlServerGrammar::class,
        self::MSSQL => SqlServerGrammar::class,
        self::NONE => NoneGrammar::class,
    ];

    /**
     * @var PDO|null
     */
    protected static $pdo;

    /**
     * @param PDO $pdo
     * @return void
     */
    public static function setPdo(PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    /**
     * @param string $sql
     * @return string
     */
    abstract public function columnize(string $sql): string;

    /**
     * @param string $value
     * @return string
     */
    public function parameterize(string $value): string
    {
        return $this->quoteString($this->escape($value));
    }

    /**
     * @param string|null $driver
     * @return Grammar
     */
    public static function factory(?string $driver = null): self
    {
        $grammar = $driver !== null && array_key_exists(strtolower($driver), static::$drivers) ? static::$drivers[$driver] : PdoGrammar::class;

        return new $grammar();
    }

    /**
     * @param string $sql
     * @param string[] $columnQuotedIdentifiers
     * @return string
     */
    protected function replaceColumnQuotedIdentifiers(string $sql, array $columnQuotedIdentifiers): string
    {
        [$left, $right] = $columnQuotedIdentifiers;

        return preg_replace_callback('/[`"\[](?<column>[^`"\[\]]+)[`"\]]/', static function ($matches) use ($right, $left) {
            return ! empty($matches['column']) ? $left.$matches['column'].$right : $matches[0];
        }, $sql);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function quoteString(string $value): string
    {
        return "'$value'";
    }

    /**
     * @param string $value
     * @return string
     */
    protected function escape(string $value): string
    {
        return $value;
    }
}
