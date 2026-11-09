<?php

declare(strict_types=1);

namespace Nexus\Monolith\Settings;

use Nexus\Monolith\Settings\Models\SettingModel;
use ReflectionClass;
use ReflectionProperty;
use Str;

abstract class SettingsGroup implements \Illuminate\Contracts\Support\Arrayable
{
    /** @param iterable<int, SettingModel> $settings */
    public function __construct(iterable $settings = [])
    {
        $properties = $this->reflectionProperties();

        foreach ($settings as $setting) {
            if ($setting->group !== static::class) {
                continue;
            }

            $property = $properties[$setting->name] ?? null;

            if (! $property instanceof ReflectionProperty) {
                continue;
            }

            $property->setValue($this, $setting->payload);
        }
    }

    abstract public function getGroupName(): ?string;

    public function defaults(): array
    {
        return [

        ];
    }

    /** @return array<string, mixed> */
    public function getAttributes(): array
    {
        $attributes = [];

        foreach ($this->reflectionProperties() as $property) {
            if (!$property->isInitialized($this)) {
                $attributes[$property->getName()] = null;
                continue;
            }
            $attributes[$property->getName()] = $property->getValue($this);
        }

        return $attributes;
    }

    /** @return array<string, ReflectionProperty> */
    private function reflectionProperties(): array
    {
        $properties = (new ReflectionClass(static::class))->getProperties(
            ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED,
        );
        $reflectedProperties = [];

        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $reflectedProperties[$property->getName()] = $property;
        }

        return $reflectedProperties;
    }

    public function moduleSlug(): string
    {
        return '';
    }

    public function groupKey(): string
    {
        $module = !empty($this->moduleSlug()) ? $this->moduleSlug() . '.' :'';
        return $module . Str::lower(Str::snake(Str::beforeLast(class_basename(static::class), 'Setting')));
    }

    public function toArray(): array
    {
        return $this->getAttributes();
    }
}
