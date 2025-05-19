<?php

namespace App\Models\ExternalModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OriginalPasante extends Model
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
    protected $table = 'pasante';

    /**
     * The primary key associated with the table.
     *
     * @var int
     */
    protected $primaryKey = 'id_pasante';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombres',
        'apellidos',
        'ci_pasante',
        'registro_universitario',
        'id_usuario',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_pasante' => 'integer',
            'ci_pasante' => 'string',
            'registro_universitario' => 'string',
            'id_usuario' => 'integer',
        ];
    }
}
