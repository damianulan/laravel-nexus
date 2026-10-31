<?php

declare(strict_types=1);

namespace Nexus\Monolith\Settings;

use Nexus\Monolith\Settings\Models\SettingModel;
use ReflectionClass;
use ReflectionProperty;

abstract class SettingsGroup
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
}
