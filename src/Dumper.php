<?php

namespace Recca0120\EloquentDumper;

use DateTime;
use Illuminate\Database\Query\Expression;
use PDO;
use Recca0120\EloquentDumper\Dumpers\MySqlDumper;
use Recca0120\EloquentDumper\Dumpers\PdoDumper;
use Recca0120\EloquentDumper\Dumpers\PostgresDumper;
use Recca0120\EloquentDumper\Dumpers\SQLiteDumper;
use Recca0120\EloquentDumper\Dumpers\SqlServerDumper;
use Recca0120\EloquentDumper\Dumpers\WithoutQuoteDumper;

abstract class Dumper
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
    public const WITHOUT_QUOTE = 'none';

    /**
     * @var PDO|null
     */
    protected $pdo;

    private static $drivers = [
        self::DEFAULT => PdoDumper::class,
        self::PDO => PdoDumper::class,
        self::MYSQL => MySqlDumper::class,
        self::SQLITE => SQLiteDumper::class,
        self::POSTGRES => PostgresDumper::class,
        self::PGSQL => PostgresDumper::class,
        self::SQLSERVER => SqlServerDumper::class,
        self::SQLSRV => SqlServerDumper::class,
        self::MSSQL => SqlServerDumper::class,
        self::WITHOUT_QUOTE => WithoutQuoteDumper::class,
    ];

    /**
     * @param PDO $pdo
     * @return self
     */
    public function setPdo(PDO $pdo): self
    {
        $this->pdo = $pdo;

        return $this;
    }

    /**
     * @param string $sql
     * @param array $bindings
     * @return string
     */
    public function dump(string $sql, array $bindings): string
    {
        return $this->bindValues($sql, $bindings);
    }

    /**
     * @param string|null $driver
     * @return Dumper
     */
    public static function factory(?string $driver = null): self
    {
        $grammar = $driver !== null && array_key_exists(strtolower($driver), static::$drivers) ? static::$drivers[$driver] : PdoDumper::class;

        return new $grammar();
    }

    /**
     * @param string $sql
     * @return string
     */
    abstract protected function columnize(string $sql): string;

    /**
     * @param string $value
     * @return string
     */
    protected function parameterize(string $value): string
    {
        return $this->quoteString($this->escape($value));
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

    /**
     * @param string $sql
     * @param array $bindings
     * @return string
     */
    private function bindValues(string $sql, array $bindings): string
    {
        return vsprintf(
            str_replace(['%', '?'], ['%%', '%s'], $this->columnize($sql)),
            array_map([$this, 'toValue'], $bindings)
        );
    }

    /**
     * @param mixed $binding
     * @return string|int
     */
    private function toValue($binding)
    {
        if (is_array($binding)) {
            return implode(', ', array_map(function ($value) {
                return $this->toValue($value);
            }, $binding));
        }

        if ($binding instanceof DateTime) {
            return $this->parameterize($binding->format('Y-m-d H:i:s'));
        }

        if (is_a($binding, Expression::class)) {
            return (string) $binding;
        }

        if (is_string($binding) || (is_object($binding) && method_exists($binding, '__toString'))) {
            return $this->parameterize((string) $binding);
        }

        if (is_numeric($binding)) {
            return $binding;
        }

        if (is_bool($binding)) {
            return $binding ? 1 : 0;
        }

        return $binding ?: 'NULL';
    }
}
