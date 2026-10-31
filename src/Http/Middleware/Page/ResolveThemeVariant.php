<?php

declare(strict_types=1);

namespace Nexus\Http\Middleware\Page;

use Nexus\Enums\Page\ThemeVariant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ResolveThemeVariant
{
    public const string COOKIE_KEY = 'theme_variant';

    private const string REQUEST_ATTRIBUTE = 'theme_variant';

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set(self::REQUEST_ATTRIBUTE, $this->resolve($request));

        return $next($request);
    }

    public static function fromRequest(Request $request): ThemeVariant
    {
        $variant = $request->attributes->get(self::REQUEST_ATTRIBUTE);

        return $variant instanceof ThemeVariant ? $variant : ThemeVariant::Light;
    }

    private function resolve(Request $request): ThemeVariant
    {
        $cookieVariant = $request->cookie(self::COOKIE_KEY);

        if (! is_string($cookieVariant)) {
            return ThemeVariant::Light;
        }

        return ThemeVariant::tryFrom($cookieVariant) ?? ThemeVariant::Light;
    }
}
