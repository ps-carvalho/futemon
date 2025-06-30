<?php

declare(strict_types=1);

namespace App\DTOs;

final class CountryImportDto
{
    public function __construct(
        public int $imported_id,
        public string $name,
        public string $official_name,
        public ?string $fifa_name,
        public ?string $iso2,
        public ?string $iso3,
        public string $longitude,
        public string $latitude,
        public string $image_path,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApiData(array $data): self
    {
        return new self(
            imported_id: $data['id'],
            name: $data['name'],
            official_name: $data['official_name'],
            fifa_name: $data['fifa_name'],
            iso2: $data['iso2'],
            iso3: $data['iso3'] ?? null,
            longitude: $data['longitude'] ?? null,
            latitude: $data['latitude'] ?? null,
            image_path: $data['image_path'] ?? null,
        );
    }
}
