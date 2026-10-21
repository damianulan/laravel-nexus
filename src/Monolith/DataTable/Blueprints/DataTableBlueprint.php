<?php

namespace Nexus\Monolith\DataTable\Blueprints;

use Closure;
use Illuminate\Http\Request;
use Nexus\Monolith\DataTable\Contracts\TableContract;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class DataTableBlueprint implements TableContract
{
    /** @var Collection<DataColumn> */
    private Collection $columns;

    private DataTablePaginator $paginator;

    /**
     * Per page available options.
     * @var array<int>
     */
    private array $pagination;

    /** @return class-string<JsonResource> */
    abstract protected function getResourceClass(): string;

    abstract protected function source(): Collection|EloquentBuilder|QueryBuilder;

    /** @return array<DataColumn> */
    abstract protected function columns(): array;

    public function __construct(?Request $request = null) {
        if ($request === null) {
            $request = FacadesRequest::capture();
        }
        $this->paginator = DataTablePaginator::from(
            $this->source(),
            $this->getResourceClass(),
            $request->query('perPage', 15),
            'page',
            $request->query('page', 1),
            $request->query('sortBy'),
            $request->query('sortDirection', 'ASC'),
        );
        $this->columns = collect($this->columns());
    }

    public function getPaginator(): DataTablePaginator
    {
        return $this->paginator;
    }

    public function toArray(): array
    {
        return [
            'columns' => $this->columns->toArray(),
            'paginator' => $this->paginator->toArray(),
            'pagination' => $this->pagination,
        ];
    }
}
