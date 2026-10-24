<?php

namespace Nexus\Http\Middleware\Inertia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Translation\FileLoader;
use Inertia\Inertia;
use Inertia\Middleware;
use Nexus\Facades\Nav\Sidebar;
use Nexus\Facades\Page\Alert;
use Nexus\Facades\Page\Page;
use Nexus\Facades\Page\Snackbar;
use Nexus\Http\Middleware\Page\ResolveThemeVariant;

class InertiaMiddleware extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

        return [
            ...parent::share($request),
            'theme' => $this->resolveTheme($request),
            'sidebar' => Sidebar::toArray(),
            'page' => Page::toArray(),
            'snackbars' => fn (): array => Snackbar::getAll(),
            'alerts' => fn (): array => Alert::getAll(),
        ];
    }

    /**
     * Define the props that are shared once and remembered across navigations.
     *
     * @return array<string, mixed>
     */
    public function shareOnce(Request $request): array
    {

        return [
            ...parent::shareOnce($request),
            ...$this->getLocalizationOnce()
        ];
    }

    protected function getLocalizationOnce(): array
    {
        $locale = app()->getLocale();

        return [
            'localization' => Inertia::once(fn (): array => [
                'locale' => $locale,
                'messages' => $this->translationMessages($locale),
            ])->as("localization.{$locale}"),
        ];
    }

    /** @return array<string, mixed> */
    private function translationMessages(string $locale): array
    {
        $loader = Lang::getLoader();

        if (! $loader instanceof FileLoader) {
            return [];
        }

        $locales = array_values(array_unique([
            app()->getFallbackLocale(),
            $locale,
        ]));

        $messages = [];

        foreach ($this->translationGroups($loader, $locales) as $group) {
            foreach ($locales as $language) {
                $messages[$group] = array_replace_recursive(
                    $messages[$group] ?? [],
                    $loader->load($language, $group),
                );
            }
        }

        foreach ($locales as $language) {
            $messages = array_replace(
                $messages,
                $loader->load($language, '*', '*'),
            );
        }

        return $messages;
    }

    /**
     * @param  array<int, string>  $locales
     * @return array<int, string>
     */
    private function translationGroups(FileLoader $loader, array $locales): array
    {
        $groups = [];

        foreach ($loader->paths() as $path) {
            foreach ($locales as $locale) {
                $directory = "{$path}/{$locale}";

                if (! File::isDirectory($directory)) {
                    continue;
                }

                foreach (File::files($directory) as $file) {
                    if ($file->getExtension() === 'php') {
                        $groups[] = $file->getFilenameWithoutExtension();
                    }
                }
            }
        }

        return array_values(array_unique($groups));
    }

    protected function resolveTheme(Request $request): array
    {
        $theme = $this->getTheme();
        return [
            ...(is_array($theme) ? $theme : []),
            'variant' => ResolveThemeVariant::fromRequest($request)->value,
            'updateUrl' => route('theme.update'),
        ];
    }

    protected function getTheme(): array
    {
        return config('theme.inkwell');
    }
}
