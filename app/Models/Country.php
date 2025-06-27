<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $official_name
 * @property-read string|null $image_path
 * @property-read string $fifa_name
 * @property-read string $iso2
 * @property-read string $iso3
 * @property-read string $latitude
 * @property-read string $longitude
 */
final class Country extends Model
{
    /** @use HasFactory<CountryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'official_name',
        'image_path',
        'fifa_name',
        'iso2',
        'iso3',
        'latitude',
        'longitude',
    ];
}
