<?php

namespace Recca0120\EloquentDumper;

use DateTime;
use Illuminate\Database\Query\Builder;
use PhpMyAdmin\SqlParser\Utils\Formatter;

class Dumper
{
    /**
     * @param Builder $query
     * @param bool $wrap
     * @return string
     */
    public function rawSql(Builder $query, $wrap = true)
    {
        return $this->format(vsprintf(
            $this->toSql($query->toSql(), $wrap),
            $this->toBindings($query->getBindings())
        ));
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
     * @param bool $wrap
     * @return string
     */
    private function toSql($sql, $wrap)
    {
        $sql = $wrap ? $sql : preg_replace_callback('/[`"\[](?<column>[^`"\[\]]+)[`"\]]/', function ($matches) {
            return ! empty($matches['column']) ? $matches['column'] : $matches[0];
        }, $sql);

        return str_replace(['%', '?'], ['%%', '%s'], $sql);
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
}
