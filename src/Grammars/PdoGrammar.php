<?php

namespace Recca0120\EloquentDumper\Grammars;

use PDO;

class PdoGrammar extends Grammar
{
    /**
     * @var PDO|null
     */
    public static $pdo;

    public static function setPdo(PDO $pdo)
    {
        self::$pdo = $pdo;
    }

    /**
     * @param string $sql
     * @return string
     */
    public function columnize(string $sql): string
    {
        return $sql;
    }

    /**
     * @param string $value
     * @return string
     */
    public function parameterize(string $value): string
    {
        return self::$pdo ? static::$pdo->quote($value) : parent::parameterize(addslashes($value));
    }
}
