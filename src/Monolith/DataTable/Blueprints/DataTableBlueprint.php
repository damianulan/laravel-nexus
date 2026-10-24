<?php

declare(strict_types=1);

namespace Nexus\Monolith\DataTable\Blueprints;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Nexus\Monolith\DataTable\Contracts\ColumnContract;
use Nexus\Monolith\DataTable\Contracts\TableContract;
use Nexus\Monolith\Filtering\Blueprints\FilterBag;
use Nexus\Monolith\Filtering\Facades\Filters;
use UnexpectedValueException;

abstract class DataTableBlueprint implements TableContract
{
    /** @var Collection<string, ColumnContract> */
    private Collection $columns;

    private DataTablePaginator $paginator;

    /**
     * Per page available options.
     * @var array<int>
     */
    private array $pagination;

    /** @return class-string<JsonResource> */
    abstract protected function getResourceClass(): string;

    /** @return Collection<covariant int|string, mixed>|Builder */
    abstract protected function source(): Collection | Builder;

    /** @return list<ColumnContract> */
    abstract protected function columns(): array;

    public function __construct(?Request $request = null)
    {
        if ($request === null) {
            $request = FacadesRequest::instance();
        }

        $source = $this->getSourceWithFilters();

        $this->paginator = DataTablePaginator::from(
            $source,
            $this->getResourceClass(),
            $request->integer('perPage', config('datatables.default_per_page')),
            'page',
            $request->integer('page', 1),
            $request->string('sortBy')->toString() ?: null,
            $request->string('sortDirection', 'ASC')->toString(),
        );
        $this->columns = collect($this->columns())
            ->mapWithKeys(fn (ColumnContract $item): array => [$item->getName() => $item]);
        $this->pagination = array_values(array_filter(
            Config::array('datatables.pagination', []),
            is_int(...),
        ));
    }

    public function getPaginator(): DataTablePaginator
    {
        return $this->paginator;
    }

    protected function getFilters(): FilterBag
    {
        return Filters::with([]);
    }

    public function toArray(): array
    {
        $paginator = $this->paginator->toArray();
        $paginator['data'] = array_map(
            fn (array $resource): array => $this->addActionButtons($resource),
            $paginator['data'],
        );

        return [
            'columns' => $this->columns->values()->toArray(),
            'paginator' => $paginator,
            'pagination' => $this->pagination,
        ];
    }

    /**
     * @param array<string, mixed> $resource
     * @return array<string, mixed>
     */
    private function addActionButtons(array $resource): array
    {
        $resourceWithActions = $resource;

        foreach ($this->columns as $column) {
            if (! $column instanceof ActionColumn) {
                continue;
            }

            $resourceWithActions[$column->getName()] = $column->buttonsFor($resource);
        }

        return $resourceWithActions;
    }

    private function getSourceWithFilters(): Collection | Builder
    {
        return $this->getFilters()->apply($this->source());
    }

    protected function label(string $key): string
    {
        $label = __($key);

        if (! is_string($label)) {
            throw new UnexpectedValueException("The translation [{$key}] must be a string.");
        }

        return $label;
    }
}
