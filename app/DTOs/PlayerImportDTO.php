<?php

declare(strict_types=1);

namespace App\DTOs;

final class PlayerImportDTO
{
    public function __construct(
        public int $imported_id,
        public string $name,
        public string $common_name,
        public string $gender,
        public string $display_name,
        public ?string $image_path,
        public CountryImportDto $country,
        public ?PositionImportDTO $position,
        public ?string $date_of_birth,
        public ?int $height,
        public ?int $weight,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApiData(array $data): self
    {
        return new self(
            imported_id: $data['id'],
            name: $data['name'],
            common_name: $data['common_name'] ?? null,
            gender: $data['gender'] ?? null,
            display_name: $data['display_name'] ?? null,
            image_path: $data['image_path'] ?? null,
            country: CountryImportDto::fromApiData($data['country']),
            position: isset($data['position']) ? PositionImportDTO::fromApiData($data['position']) : null,
            date_of_birth: $data['date_of_birth'] ?? null,
            height: $data['height'] ?? null,
            weight: $data['weight'] ?? null,
        );
    }
}
