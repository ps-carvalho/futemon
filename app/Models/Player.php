<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SortDirection;
use App\Traits\NormalizesNames;
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

    use NormalizesNames;

    protected $fillable = [
        'imported_id',
        'position_id',
        'country_id',
        'name',
        'normalized_name',
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
            ->each(fn ($word) => $query->where('normalized_name', 'LIKE', sprintf('%%%s%%', mb_strtolower($word))));

        return $query;
    }

    /**
     * @param  Builder<Player>  $query
     * @return Builder<Player>
     */
    public function scopeOrderByNameNormalized(Builder $query, string $direction = SortDirection::ASC->value): Builder
    {
        $validDirection = in_array(mb_strtoupper($direction), [
            SortDirection::ASC->value,
            SortDirection::DESC->value,
        ]) ? mb_strtoupper($direction) : SortDirection::ASC->value;

        return $query->orderBy('normalized_name', $validDirection);

    }

    public function getAge(): int
    {
        if ($this->date_of_birth === null) {
            return 0;
        }

        return $this->date_of_birth->age;
    }

    /**
     * Boot method to automatically normalize names
     */
    protected static function boot(): void
    {
        parent::boot();

        self::saving(function ($player): void {
            $player->normalized_name = self::normalizeName($player->name);
        });
    }
}
