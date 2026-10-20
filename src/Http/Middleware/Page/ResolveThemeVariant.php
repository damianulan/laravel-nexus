<?php

declare(strict_types=1);

namespace Nexus\Http\Middleware\Page;

use Nexus\Enums\Page\ThemeVariant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ResolveThemeVariant
{
    public const string SESSION_KEY = 'theme_variant';

    private const string REQUEST_ATTRIBUTE = 'theme_variant';

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $variant = $this->resolve($request);

        $request->session()->put(self::SESSION_KEY, $variant->value);
        $request->attributes->set(self::REQUEST_ATTRIBUTE, $variant);

        return $next($request);
    }

    public static function fromRequest(Request $request): ThemeVariant
    {
        $variant = $request->attributes->get(self::REQUEST_ATTRIBUTE);

        return $variant instanceof ThemeVariant ? $variant : ThemeVariant::Light;
    }

    private function resolve(Request $request): ThemeVariant
    {
        $user = $request->user();

        $sessionVariant = $request->session()->get(self::SESSION_KEY);

        if (! is_string($sessionVariant)) {
            return ThemeVariant::Light;
        }

        return ThemeVariant::tryFrom($sessionVariant) ?? ThemeVariant::Light;
    }
}
