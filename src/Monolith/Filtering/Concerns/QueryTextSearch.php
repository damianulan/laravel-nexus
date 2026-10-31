<?php

namespace Nexus\Monolith\Filtering\Concerns;

use Illuminate\Contracts\Database\Query\Builder;
use Str;

trait QueryTextSearch
{
    /**
     * @param  Builder  $query
     * @param  string  $text
     * @param  array<int, string>  $fields
     */
    protected function searchText(
        Builder $query,
        ?string $text,
        array $fields,
        bool $leftSearch = true,
        bool $rightSearch = true
        ): void
    {
        if ($fields === [] || blank($text)) {
            return;
        }

        $terms = preg_split('/\s+/u', Str::trim($text), flags: PREG_SPLIT_NO_EMPTY);

        $left = $leftSearch ? '%' : '';
        $right = $rightSearch ? '%' : '';

        foreach ($terms ?: [] as $term) {
            $query->where(function (Builder $query) use ($fields, $term, $left, $right) {
                foreach ($fields as $field) {
                    $query->orWhere($field, 'LIKE', "{$left}{$term}{$right}");
                }
            });
        }
    }

    protected function nameSearch(Builder $query, ?string $text, array $fields): void
    {
        $this->searchText($query, $text, $fields, false, true);
    }
}
