<?php

declare(strict_types=1);

namespace Nexus\Monolith\Settings;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Collection;
use Nexus\Monolith\Settings\Models\SettingModel;
use ReflectionClass;
use Spatie\StructureDiscoverer\Discover;

class Factory
{
    public const string CACHE_KEY = 'nexus.settings';

    public const int CACHE_TTL_SECONDS = 86_400;

    /** @param array<int, string> $discoveryPaths */
    public function __construct(
        private readonly Repository $cache,
        private readonly array $discoveryPaths,
    ) {
    }

    public function __invoke(): SettingsCollection
    {
        return $this->get();
    }

    public function get(): SettingsCollection
    {
        /** @var SettingsCollection $settings */
        $settings = $this->cache->remember(
            self::CACHE_KEY,
            self::CACHE_TTL_SECONDS,
            fn (): SettingsCollection => $this->load(),
        );

        return $settings;
    }

    public function forget(): bool
    {
        return $this->cache->forget(self::CACHE_KEY);
    }

    protected function load(): SettingsCollection
    {
        $groupClasses = $this->discoverGroupClasses();

        if ($groupClasses === []) {
            return new SettingsCollection;
        }

        $settings = $this->settingsFor($groupClasses)->groupBy('group');
        $groups = [];

        foreach ($groupClasses as $groupClass) {
            $groups[$groupClass] = new $groupClass($settings->get($groupClass, []));
        }

        return new SettingsCollection($groups);
    }

    /** @return array<int, class-string<SettingsGroup>> */
    protected function discoverGroupClasses(): array
    {
        $paths = array_values(array_filter($this->discoveryPaths, is_dir(...)));

        if ($paths === []) {
            return [];
        }

        $classes = Discover::in(...$paths)
            ->classes()
            ->extending(SettingsGroup::class)
            ->get();

        return array_values(array_filter(
            $classes,
            fn (string $class): bool => (new ReflectionClass($class))->isInstantiable(),
        ));
    }

    /**
     * @param array<int, class-string<SettingsGroup>> $groupClasses
     * @return Collection<int, SettingModel>
     */
    protected function settingsFor(array $groupClasses): Collection
    {
        return SettingModel::query()
            ->whereIn('group', $groupClasses)
            ->get();
    }
}
