<?php

namespace App\Models\ExternalModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OriginalCargo extends Model
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
    protected $table = 'cargo';

    /**
     * The primary key associated with the table.
     *
     * @var int
     */
    protected $primaryKey = 'id_cargo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre_cargo',
        'id_area',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_cargo' => 'integer',
        ];
    }
}
