<?php

namespace Recca0120\EloquentDumper\Grammars;

use PDO;
use function Sodium\add;

class PdoGrammar extends Grammar
{
    /**
     * @var PDO|null
     */
    public static $pdo = null;

    public static function setPdo(PDO $pdo)
    {
        self::$pdo = $pdo;
    }

    /**
     * @param string $sql
     * @return string
     */
    public function columnize($sql)
    {
        return $sql;
    }

    /**
     * @param string $value
     * @return string
     */
    public function parameterize($value)
    {
        return self::$pdo ? static::$pdo->quote($value) : $this->quoteString(addslashes($value));
    }
}
