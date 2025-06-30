<?php

declare(strict_types=1);

namespace App\DTOs;


class PositionImportDTO
{
    public function __construct(
        public int $imported_id,
        public string $name,
        public string $code,
        public string $developer_name,
        public string $model_type,
        public ?string $stat_group,

    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApiData(array $data): self
    {
        return new self(
            imported_id: $data['id'],
            name: $data['name'],
            code: $data['code'],
            developer_name: $data['developer_name'],
            model_type: $data['model_type'],
            stat_group: $data['stat_group'],
        );
    }
}
