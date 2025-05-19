<?php

namespace App\Models\ExternalModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OriginalSupervisor extends Model
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
    protected $table = 'supervisor';
    /**
     * The primary key associated with the table.
     *
     * @var int
     */
    protected $primaryKey = 'id_supervisor';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ci_supervisor',
        'grado_academico_supervisor',
        'nombre_supervisor',
        'partido_supervisor',
        'materno_supervisor',
        'area_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_supervisor' => 'integer',
            'ci_supervisor' => 'string',
            'grado_academico_supervisor' => 'string',
            'nombre_supervisor' => 'string',
            'partido_supervisor' => 'string',
            'materno_supervisor' => 'string',
            'area_id' => 'integer',
        ];
    }
}
