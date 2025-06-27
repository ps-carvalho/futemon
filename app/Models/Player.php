<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PlayerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int $sports_monk_id
 * @property-read int $position_id
 * @property-read int $country_id
 * @property-read string $name
 * @property-read string $common_name
 * @property-read string $display_name
 * @property-read string $gender
 * @property-read string $image_path
 * @property-read string $date_of_birth
 * @property-read int $height
 * @property-read int $weight
 */
final class Player extends Model
{
    /** @use HasFactory<PlayerFactory> */
    use HasFactory;

    protected $fillable = [
        'sports_monk_id',
        'position_id',
        'country_id',
        'name',
        'common_name',
        'display_name',
        'gender',
        'image_path',
        'date_of_birth',
        'height',
        'weight',
    ];

    /**
     * Get the player country.
     *
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);

    }

    /**
     * Get the player position.
     *
     * @return BelongsTo<PlayerPosition, $this>
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(PlayerPosition::class);
    }
}
