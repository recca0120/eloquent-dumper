<?php

namespace Recca0120\EloquentDumper;

use DateTime;
use PhpMyAdmin\SqlParser\Utils\Formatter;

class Dumper
{
    const DEFAULT = 'default';
    const NONE = 'none';
    const MYSQL = 'mysql';
    const SQLITE = 'sqlite';
    const POSTGRES = 'postgres';
    const MSSQL = 'mssql';

    /**
     * @var null
     */
    private $driver;

    /**
     * Dumper constructor.
     * @param string $driver
     */
    public function __construct($driver = 'default')
    {
        $this->driver = $driver;
    }

    /**
     * @param string $driver
     * @return $this
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;

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
        return str_replace(['%', '?'], ['%%', '%s'], $this->quoteSql($sql));
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
                    return is_string($value) === true ? $this->value($value) : $value;
                }, $binding));
            }

            if (is_string($binding)) {
                return $this->value($binding);
            }

            if ($binding instanceof DateTime) {
                return $this->value($binding->format('Y-m-d H:i:s'));
            }

            if (is_object($binding) && method_exists($binding, '__toString')) {
                return $this->value($binding->__toString());
            }

            return $binding;
        }, $bindings);
    }

    /**
     * @param $binding
     * @return string
     */
    private function value($binding)
    {
        return sprintf("'%s'", $binding);
    }

    /**
     * @param string $sql
     * @return string
     */
    private function quoteSql(string $sql)
    {
        if (in_array($this->driver, [null, 'default'], true)) {
            return $sql;
        }

        [$left, $right] = $this->getQuote();

        return preg_replace_callback('/[`"\[](?<column>[^`"\[\]]+)[`"\]]/', function ($matches) use ($right, $left) {
            return !empty($matches['column']) ? $left . $matches['column'] . $right : $matches[0];
        }, $sql);
    }

    /**
     * @return string[]
     */
    private function getQuote()
    {
        $quoteLookup = [
            self::NONE => ['', ''],
            self::MYSQL => ['`', '`'],
            self::SQLITE => ['"', '"'],
            self::POSTGRES => ['"', '"'],
            self::MSSQL => ['[', ']'],
        ];

        return $quoteLookup[$this->driver];
    }
}
