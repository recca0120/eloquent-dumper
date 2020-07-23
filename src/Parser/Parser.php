<?php

namespace Recca0120\EloquentDumper\Parser;

abstract class Parser
{
    private static $lookup = [
        'mysql' => 'MySQL',
        'none' => 'None',
        'postgres' => 'Postgres',
        'sqlite' => 'sqlite',
        'sqlserver' => 'SqlServer',
        'mssql' => 'SqlServer',
    ];

    /**
     * @param string $sql
     * @return string
     */
    abstract public function columnize($sql);

    /**
     * @param string|null $driver
     * @return Parser
     */
    public static function factory($driver)
    {
        $driver = $driver !== null && array_key_exists(strtolower($driver), static::$lookup)
            ? __NAMESPACE__.'\\'.static::$lookup[strtolower($driver)]
            : Defaults::class;

        return new $driver();
    }

    /**
     * @param string $sql
     * @param string[] $columnQuotedIdentifiers
     * @return string
     */
    protected function replaceColumnQuotedIdentifiers($sql, $columnQuotedIdentifiers)
    {
        [$left, $right] = $columnQuotedIdentifiers;

        return preg_replace_callback('/[`"\[](?<column>[^`"\[\]]+)[`"\]]/', function ($matches) use ($right, $left) {
            return ! empty($matches['column']) ? $left.$matches['column'].$right : $matches[0];
        }, $sql);
    }
}
