<?php

namespace Nexus\Monolith\DataTable\Blueprints;

use Closure;
use Illuminate\Http\Request;
use Nexus\Monolith\DataTable\Contracts\TableContract;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request as FacadesRequest;

class DataTableBlueprint implements TableContract
{
    /** @var Collection<DataColumn> */
    protected Collection $columns;

    protected DataTablePaginator $paginator;

    protected string $resourceClass;

    protected Collection $actions;

    public function make(
        Collection|EloquentBuilder|QueryBuilder $source,
        string $resourceClass,
    ): static {
        $instance = new static();
        $request = FacadesRequest::capture();
        $instance->resourceClass = $resourceClass;
        $instance->paginator = DataTablePaginator::from(
            $source,
            $resourceClass,
            $request->query('perPage', 15),
            'page',
            $request->query('page', 1),
            $request->query('sortBy'),
            $request->query('sortDirection', 'ASC'),
        );
        $instance->columns = new Collection();
        $instance->actions = new Collection();

        return $instance;
    }

    public function getPaginator(): DataTablePaginator
    {
        return $this->paginator;
    }

    public function addColumn(
        string $name,
        string $label,
        ?string $icon = null,
        ?Closure $condition = null
    ): static
    {
        if ($condition !== null && !$condition($name)) {
            return $this;
        }
        $this->columns->push(new DataColumn($name, $label, $icon));
        return $this;
    }

    public function toArray(): array
    {
        return [
            'columns' => $this->columns->toArray(),
            'paginator' => $this->paginator->toArray(),
            'actions' => $this->actions->toArray(),
        ];
    }
}
