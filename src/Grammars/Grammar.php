<?php

namespace Recca0120\EloquentDumper\Grammars;

abstract class Grammar
{
    private static $lookup = [
        'mysql' => MySqlGrammar::class,
        'none' => NoneGrammar::class,
        'postgres' => PostgresGrammar::class,
        'sqlite' => SQLiteGrammar::class,
        'sqlserver' => SqlServerGrammar::class,
        'mssql' => SqlServerGrammar::class,
    ];

    /**
     * @param string $sql
     * @return string
     */
    abstract public function columnize($sql);

    /**
     * @param string $value
     * @return string
     */
    abstract public function parameterize($value);

    /**
     * @param string|null $driver
     * @return Grammar
     */
    public static function factory($driver)
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
    protected function replaceColumnQuotedIdentifiers($sql, $columnQuotedIdentifiers)
    {
        $left = $columnQuotedIdentifiers[0];
        $right = $columnQuotedIdentifiers[1];

        return preg_replace_callback('/[`"\[](?<column>[^`"\[\]]+)[`"\]]/', function ($matches) use ($right, $left) {
            return ! empty($matches['column']) ? $left.$matches['column'].$right : $matches[0];
        }, $sql);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function quoteString($value)
    {
        return "'$value'";
    }
}
