<?php

declare(strict_types=1);

namespace Nexus\Monolith\DataTable\Enums;

enum ColumnType: string
{
    case Action = 'action';
    case Default = 'default';
    case Selectable = 'selectable';
}
