<?php

namespace Recca0120\EloquentDumper;

use DateTime;
use Illuminate\Database\Query\Expression;
use PDO;
use PhpMyAdmin\SqlParser\Utils\Formatter;
use Recca0120\EloquentDumper\Grammars\Grammar;

class Dumper
{
    /**
     * @var Grammar|null
     */
    private $grammar;

    /**
     * Dumper constructor.
     * @param string $grammar
     */
    public function __construct(string $grammar = Grammar::PDO)
    {
        $this->setGrammar($grammar);
    }

    /**
     * @param PDO $pdo
     * @return Dumper
     */
    public function setPdo(PDO $pdo): self
    {
        Grammar::setPdo($pdo);

        return $this;
    }

    /**
     * @param string|null $grammar
     * @return Dumper
     */
    public function setGrammar(?string $grammar): self
    {
        $this->grammar = Grammar::factory($grammar);

        return $this;
    }

    /**
     * @param string $sql
     * @param array $bindings
     * @param bool $format
     * @return string
     */
    public function dump(string $sql, array $bindings, bool $format = false): string
    {
        $raw = $this->bindValues($sql, $bindings);

        return $format ? Formatter::format($raw) : $raw;
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
            return $this->grammar->parameterize($binding->format('Y-m-d H:i:s'));
        }

        if (is_a($binding, Expression::class)) {
            return (string) $binding;
        }

        if (is_string($binding) || (is_object($binding) && method_exists($binding, '__toString'))) {
            return $this->grammar->parameterize((string) $binding);
        }

        if (is_bool($binding)) {
            return $binding ? 1 : 0;
        }

        return $binding ?: 'NULL';
    }
}
