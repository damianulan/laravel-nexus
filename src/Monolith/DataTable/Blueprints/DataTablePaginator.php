<?php

namespace Nexus\Monolith\DataTable\Blueprints;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DataTablePaginator extends LengthAwarePaginator
{
    protected ?string $sortBy;

    protected ?string $sortDirection;

    /**
     * @param class-string<JsonResource> $resourceClass
     */
    public function __construct(
        Collection $items,
        int $total,
        int $perPage,
        ?int $currentPage,
        string $resourceClass,
        array $options = [],
        ?string $sortBy = null,
        ?string $sortDirection = null,
    ) {
        parent::__construct(
            $this->mapCollection($items, $resourceClass),
            $total,
            $perPage,
            $currentPage,
            $options,
        );

        $this->sortBy = $sortBy;
        $this->sortDirection = $sortDirection;
    }

    /**
     * @param class-string<JsonResource> $resourceClass
     */
    public static function from(
        Collection|EloquentBuilder|QueryBuilder $source,
        string $resourceClass,
        int $perPage = 15,
        string $pageName = 'page',
        ?int $page = null,
        ?string $sortBy = null,
        ?string $sortDirection = null,
    ): static {
        $page ??= static::resolveCurrentPage($pageName);

        if ($source instanceof Collection) {
            return new static(
                items: $source->forPage($page, $perPage)->values(),
                total: $source->count(),
                perPage: $perPage,
                currentPage: $page,
                resourceClass: $resourceClass,
                options: [
                    'path' => static::resolveCurrentPath(),
                    'pageName' => $pageName,
                ],
                sortBy: $sortBy,
                sortDirection: $sortDirection,
            );
        }

        $paginator = $source->paginate(
            perPage: $perPage,
            pageName: $pageName,
            page: $page,
        );

        return new static(
            items: $paginator->getCollection(),
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
            resourceClass: $resourceClass,
            options: $paginator->getOptions(),
            sortBy: $sortBy,
            sortDirection: $sortDirection,
        );
    }

    protected static function sort(
        Collection|EloquentBuilder|QueryBuilder $source,
        ?string $sortBy,
        ?string $sortDirection
    ): Collection|EloquentBuilder|QueryBuilder
    {
        $sortDirection ??= 'asc';
        $sortDirection = mb_strtolower($sortDirection);
        if ($sortBy === null) {
            return $source;
        }
        if ($source instanceof Collection) {
            return $source->sortBy([$sortBy, $sortDirection]);
        }

        return $source->orderBy($sortBy, $sortDirection);
    }

    private function mapCollection(Collection $items, string $resourceClass): Collection
    {
        return $items->map(fn ($item): JsonResource => new $resourceClass($item));
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'sortBy' => $this->sortBy,
            'sortDirection' => $this->sortDirection,
        ];
    }
}
