<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PlayerPositionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $imported_id
 * @property string $name
 * @property string $code
 * @property string $developer_name
 * @property string $model_type
 * @property string $stat_group
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
