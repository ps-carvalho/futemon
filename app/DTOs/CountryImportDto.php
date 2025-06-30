<?php

declare(strict_types=1);

namespace App\DTOs;

class CountryImportDto
{
    public function __construct(
        public int $imported_id,
        public ?string $name,
        public ?string $official_name,
        public ?string $fifa_name,
        public ?string $iso2,
        public ?string $iso3,
        public float $longitude,
        public float $latitude,
        public ?string $image_path,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApiData(array $data): self
    {
        return new self(
            imported_id: $data['id'],
            name: $data['name'],
            official_name: $data['official_name'] ?? null,
            fifa_name: $data['fifa_name'] ?? null,
            iso2: $data['iso2'] ?? null,
            iso3: $data['iso3'] ?? null,
            longitude: self::toFloat($data['longitude']),
            latitude: self::toFloat($data['latitude']),
            image_path: $data['image_path'] ?? null,
        );
    }

    private static function toFloat(?string $value): float
    {
        if ($value === '' || $value === null) {
            return 0.0;
        }

        return (float) $value;
    }
}
