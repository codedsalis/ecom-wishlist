<?php

namespace App\Dtos;

namespace App\Dtos;

use ReflectionClass;
use ReflectionNamedType;

/** @phpstan-consistent-constructor */
readonly class BaseDto
{
    /** @phpstan-ignore-next-lines */
    public function __construct(...$args)
    {
    }

    /**
     * Automatically map array data into DTO, including nested DTOs.
     */
    public static function fromArray(array $data): static
    {
        $reflection = new ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();
        if (!$constructor) {
            return new static(...$data);
        }

        $args = [];

        foreach ($constructor->getParameters() as $param) {
            $name = $param->getName();

            if (!array_key_exists($name, $data)) {
                $args[] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
                continue;
            }

            $value = $data[$name];
            $type = $param->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $typeName = $type->getName();

                if (is_subclass_of($typeName, self::class)) {
                    $args[] = $typeName::fromArray($value);
                    continue;
                }

                // Handle array of DTOs
                if (is_array($value) && self::isArrayOfDtos($typeName, $value)) {
                    $args[] = array_map(fn ($item) => $typeName::fromArray($item), $value);
                    continue;
                }
            }

            $args[] = $value;
        }

        return new static(...$args);
    }

    protected static function isArrayOfDtos(string $typeName, array $items): bool
    {
        return isset($items[0]) && is_array($items[0]) && is_subclass_of($typeName, self::class);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }


    /**
     * Transform a deeply nested validated array (like from FormRequest)
     * into a flattened DTO-friendly shape with typed nested arrays.
     */
    public static function transformValidatedToDtoFormat(array $data): array
    {
        $output = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Check if this is an associative array (not a list of items)
                $isAssoc = array_keys($value) !== range(0, count($value) - 1);

                if ($isAssoc) {
                    // Flatten one level (e.g. performance.task.guage -> task_guage)
                    foreach ($value as $subKey => $subValue) {
                        if (is_array($subValue)) {
                            // If it's still deeply nested (e.g. behaviour: { from, to })
                            $output[$key][$subKey] = $subValue;
                        } else {
                            // Flatten directly
                            $output[$key]["{$subKey}"] = $subValue;
                        }
                    }
                } else {
                    // It's a list of things (like periods), preserve as-is
                    $output[$key] = $value;
                }
            } else {
                $output[$key] = $value;
            }
        }

        return $output;
    }
}
