<?php

declare(strict_types=1);

namespace Nexus\Monolith\Settings;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Collection;
use Nexus\Monolith\Settings\Models\SettingModel;
use ReflectionClass;
use Spatie\StructureDiscoverer\Discover;
use Str;

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
        $cachedGroups = $this->cache->get(self::CACHE_KEY);

        if (! is_array($cachedGroups)) {
            $this->cache->forget(self::CACHE_KEY);
        }

        /** @var array<string, array{class: class-string<SettingsGroup>, attributes: array<string, mixed>}> $groups */
        $groups = $this->cache->remember(
            self::CACHE_KEY,
            self::CACHE_TTL_SECONDS,
            fn (): array => $this->cacheableGroups(),
        );

        return new SettingsCollection(
            collect($groups)->map(fn (array $group): SettingsGroup => $this->hydrateGroup($group)),
        );
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
            /**
             * @var SettingsGroup $group
             */
            $group = new $groupClass($settings->get($groupClass, []));
            $groups[$group->groupKey()] = $group;
        }

        return new SettingsCollection($groups);
    }

    /** @return array<string, array{class: class-string<SettingsGroup>, attributes: array<string, mixed>}> */
    private function cacheableGroups(): array
    {
        return $this->load()
            ->map(fn (SettingsGroup $group): array => [
                'class' => $group::class,
                'attributes' => $group->getAttributes(),
            ])
            ->all();
    }

    /** @param array{class: class-string<SettingsGroup>, attributes: array<string, mixed>} $group */
    private function hydrateGroup(array $group): SettingsGroup
    {
        $settings = collect($group['attributes'])->map(
            fn (mixed $payload, string $name): SettingModel => SettingModel::firstOrCreate([
                'group' => $group['class'],
                'name' => $name,
            ], ['payload' => $payload]),
        );

        return new $group['class']($settings);
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

    public function find(string $key): mixed
    {
        $property = Str::afterLast($key, '.');
        $group = Str::beforeLast($key, '.');
        $settingsAttributes = $this->get()->get($group)?->getAttributes() ?? [];

        return $settingsAttributes[$property] ?? null;
    }
}
