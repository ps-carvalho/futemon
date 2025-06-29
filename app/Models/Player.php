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
 * @property int $imported_id
 * @property ?int $position_id
 * @property int $country_id
 * @property string $name
 * @property string $common_name
 * @property string $display_name
 * @property string $gender
 * @property string $image_path
 * @property ?Carbon $date_of_birth
 * @property int $height
 * @property int $weight
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

    protected $casts = [
        'date_of_birth' => 'date',
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
        if ($this->date_of_birth === null) {
            return 0;
        }

        return $this->date_of_birth->age;
    }
}
