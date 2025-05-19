<?php

namespace App\Models\ExternalModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OriginalRegistroPasante extends Model
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
    protected $table = 'registro_pasante';

    /**
     * The primary key associated with the table.
     *
     * @var int
     */
    protected $primaryKey = 'id_registro';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'pasante_id',
        'area_id',
        'tipo_pasante_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_registro' => 'integer',
            'fecha_inicio' => 'datetime',
            'fecha_fin' => 'datetime',
            'pasante_id' => 'integer',
            'area_id' => 'integer',
            'tipo_pasante_id' => 'integer',
        ];
    }
}
