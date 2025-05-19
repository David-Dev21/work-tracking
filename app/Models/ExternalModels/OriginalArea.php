<?php

namespace App\Models\ExternalModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OriginalArea extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mariadb2';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'area';
    /**
     * The primary key associated with the table.
     *
     * @var int
     */
    protected $primaryKey = 'id_area';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre_area',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_area' => 'integer',
        ];
    }
}
