<?php

namespace Nexus\Monolith\Filtering\Blueprints;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Database\Query\Builder;
use Nexus\Monolith\Filtering\Contracts\FilterInstance;

class FilterBag implements Arrayable
{
    protected array $filters = [];

    /** @var array<string, mixed> */
    protected array $payload = [];

    public function __construct(Request $request)
    {
        $this->payload = $request->input('filters', []);
    }

    /**
     * @param class-string<Filter> $filter
     * @param int $columns
     */
    public function add(
        string $filter,
        int $columns = 6,
        ?Closure $when = null,
    ): self
    {
        if ($when !== null && ! $when($this->payload)) {
            return $this;
        }
        $this->filters[] = [
            'filter' => new $filter($this->payload),
            'col' => $columns
        ];
        return $this;
    }

    public function with(array $filters): self
    {
        foreach ($filters as $filter) {
            $this->add($filter);
        }
        return $this;
    }

    /**
     * @return FilterInstance[]
     */
    public function getFilterObjects(): array
    {
        return array_map(fn (array $filter): FilterInstance => $filter['filter'], $this->filters);
    }

    public function apply(Collection | Builder $source): Collection | Builder
    {
        $method = 'withQuery';
        if ($source instanceof Collection) {
            $method = 'withCollection';
        }

        foreach ($this->getFilterObjects() as $filter) {
            $filter->$method($source);
        }

        return $source;
    }

    public function toArray(): array
    {
        return array_map(
            fn (array $filter): array => [
                'filter' => $filter['filter']->toArray(), 'col' => $filter['col']
            ],
            $this->filters
        );
    }
}
