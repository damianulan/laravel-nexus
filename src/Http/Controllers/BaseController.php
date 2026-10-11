<?php

namespace Nexus\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Nexus\Concerns\Page\FlashesMessages;
use Nexus\Facades\Page\Page;

class BaseController extends Controller
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
    use FlashesMessages;

    protected function setHeader(string $header): void
    {
        Page::setHeader($header);
    }
}
