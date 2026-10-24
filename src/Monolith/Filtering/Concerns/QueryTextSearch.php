<?php

namespace Nexus\Monolith\Filtering\Concerns;

use Illuminate\Contracts\Database\Query\Builder;

trait QueryTextSearch
{
    /**
     * @param  Builder  $query
     * @param  string  $text
     * @param  array<int, string>  $fields
     */
    protected function searchText(
        Builder $query,
        string $text,
        array $fields,
        bool $leftSearch = true,
        bool $rightSearch = true
        ): void
    {
        if ($fields === []) {
            return;
        }

        $grammar = $query->getGrammar();
        $wrappedFields = array_map(
            fn (string $field): string => $grammar->wrap($field),
            $fields,
        );
        $concatenatedFields = $query->getConnection()->getDriverName() === 'sqlite'
            ? implode(" || ' ' || ", array_map(
                fn (string $field): string => "coalesce({$field}, '')",
                $wrappedFields,
            ))
            : "concat_ws(' ', " . implode(', ', $wrappedFields) . ')';
        $terms = preg_split('/\s+/', trim($text), flags: PREG_SPLIT_NO_EMPTY);
        $left = $leftSearch ? "%" : "";
        $right = $rightSearch ? "%" : "";

        foreach ($terms ?: [] as $term) {
            $query->whereRaw("{$concatenatedFields} like ?", ["{$left}{$term}{$right}"]);
        }
    }

    protected function nameSearch(Builder $query, string $text, array $fields): void
    {
        $this->searchText($query, $text, $fields, false, true);
    }
}
