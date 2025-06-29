<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PlayerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property-read int $imported_id
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
        'imported_id',
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

    /**
     * @param  Builder<Player>  $query
     * @return Builder<Player>
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        collect(explode(' ', $term))
            ->each(fn ($word) => $query->whereRaw('LOWER(name) LIKE LOWER(?)', [sprintf('%%%s%%', $word)]));

        return $query;
    }

    public function getDisplayNationality(): string
    {
        return $this->country()->first()->name;
    }

    public function getDisplayPosition(): string
    {
        return $this->position()->first()->name;
    }

    public function getAge(): int
    {
        return Carbon::parse($this->date_of_birth)->age;
    }
}
