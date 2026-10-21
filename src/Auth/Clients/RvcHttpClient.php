<?php

namespace Nexus\Auth\Clients;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class RvcHttpClient
{
    public function send(array $credentials): Response
    {
        return Http::baseUrl(config('nexus.auth.rvc.url'))
            ->withToken(config('nexus.auth.rvc.token'))
            ->acceptJson()
            ->connectTimeout(3)
            ->timeout(10)
            ->post(config('nexus.auth.rvc.path'), $credentials);
    }

    public function getAuthenticatableUser(array $credentials): array
    {
        return $this->send($credentials)->throw()->json();
    }
}
