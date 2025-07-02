<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $imported_id
 * @property string $name
 * @property string $official_name
 * @property string|null $image_path
 * @property string $fifa_name
 * @property string $iso2
 * @property string $iso3
 * @property string $latitude
 * @property string $longitude
 */
final class Country extends Model
{
    /** @use HasFactory<CountryFactory> */
    use HasFactory;

    protected $fillable = [
        'imported_id',
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
