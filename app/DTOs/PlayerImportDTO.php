<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Exceptions\ValidationException;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;

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
        public ?Carbon $date_of_birth,
        public ?int $height,
        public ?int $weight,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws Exception
     */
    public static function fromApiData(array $data): self
    {
        $validator = Validator::make($data, [
            'id' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'common_name' => 'nullable|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'gender' => 'required|string|in:male,female',
            'image_path' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date_format:Y-m-d',
            'country' => 'required|array',
            'country.id' => 'required|integer|min:1',
            'country.name' => 'required|string|max:255',
            'country.official_name' => 'nullable|string|max:255',
            'country.fifa_name' => 'nullable|string|max:255',
            'country.iso2' => 'nullable|string|size:2',
            'country.iso3' => 'nullable|string|size:3',
            'country.longitude' => 'nullable|numeric|between:-180,180',
            'country.latitude' => 'nullable|numeric|between:-90,90',
            'position' => 'nullable|array',
            'position.id' => 'required_with:position|integer|min:1',
            'position.name' => 'required_with:position|string|max:255',
            'position.code' => 'required_with:position|string|max:10',
            'position.developer_name' => 'nullable|string|max:255',
            'position.model_type' => 'nullable|string|max:255',
            'position.stat_group' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator->getMessageBag()->first() ?? 'Invalid data received from API');
        }

        return new self(
            imported_id: $data['id'],
            name: $data['name'],
            common_name: $data['common_name'] ?? null,
            gender: $data['gender'] ?? null,
            display_name: $data['display_name'] ?? null,
            image_path: $data['image_path'] ?? null,
            country: CountryImportDto::fromApiData($data['country']),
            position: isset($data['position']) ? PositionImportDTO::fromApiData($data['position']) : null,
            date_of_birth: self::parseDateOfBirth($data['date_of_birth']),
            height: self::toInteger($data['height']),
            weight: self::toInteger($data['weight']),
        );
    }

    private static function toInteger(?int $value): int
    {
        if ($value === null) {
            return 0;
        }

        return $value;
    }

    private static function parseDateOfBirth(?string $dateOfBirth): ?Carbon
    {
        if ($dateOfBirth === null || $dateOfBirth === '' || $dateOfBirth === '0') {
            return null;
        }

        return Carbon::parse($dateOfBirth);
    }
}
