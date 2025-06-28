<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PlayerPositionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property-read int $imported_id
 * @property-read string $name
 * @property-read string $code
 * @property-read string $developer_name
 * @property-read string $model_type
 * @property-read string $stat_group
 */
final class PlayerPosition extends Model
{
    /** @use HasFactory<PlayerPositionFactory> */
    use HasFactory;

    protected $table = 'player_position';

    protected $fillable = [
        'imported_id',
        'name',
        'code',
        'developer_name',
        'model_type',
        'stat_group',
    ];
}
