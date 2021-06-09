<?php

namespace Recca0120\EloquentDumper;

use DateTime;
use Illuminate\Database\Query\Expression;
use Recca0120\EloquentDumper\Grammars\Grammar;

class Converter
{
    /**
     * @var Grammar
     */
    private $grammar;

    /**
     * Caster constructor.
     * @param Grammar $grammar
     */
    public function __construct(Grammar $grammar)
    {
        $this->grammar = $grammar;
    }

    /**
     * @param $binding
     * @return int|string
     */
    public function handle($binding)
    {
        if (is_array($binding)) {
            return implode(', ', array_map(function ($value) {
                return $this->handle($value);
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

        return $binding ?? 'NULL';
    }
}
