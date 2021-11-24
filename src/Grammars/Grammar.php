<?php

namespace Recca0120\EloquentDumper\Grammars;

use Recca0120\EloquentDumper\Dumper;

abstract class Grammar
{
    private static $lookup = [
        Dumper::DEFAULT => PdoGrammar::class,
        Dumper::PDO => PdoGrammar::class,
        Dumper::MYSQL => MySqlGrammar::class,
        Dumper::SQLITE => SQLiteGrammar::class,
        Dumper::POSTGRES => PostgresGrammar::class,
        Dumper::PGSQL => PostgresGrammar::class,
        Dumper::SQLSERVER => SqlServerGrammar::class,
        Dumper::SQLSRV => SqlServerGrammar::class,
        Dumper::MSSQL => SqlServerGrammar::class,
        Dumper::NONE => NoneGrammar::class,
    ];

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
    public static function factory(string $driver = null): Grammar
    {
        $driver = $driver !== null && array_key_exists(strtolower($driver), static::$lookup)
            ? static::$lookup[strtolower($driver)]
            : PdoGrammar::class;

        return new $driver();
    }

    /**
     * @param string $sql
     * @param string[] $columnQuotedIdentifiers
     * @return string
     */
    protected function replaceColumnQuotedIdentifiers(string $sql, array $columnQuotedIdentifiers): string
    {
        list($left, $right) = $columnQuotedIdentifiers;

        return preg_replace_callback('/[`"\[](?<column>[^`"\[\]]+)[`"\]]/', function ($matches) use ($right, $left) {
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
