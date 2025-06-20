<?php

namespace App\Models;

use App\Traits\HasAreaScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Project extends Model
{
    use HasFactory, SoftDeletes, HasAreaScope;

    // Removed booted method - no longer syncing with advance column

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'area_id',
        'name',
        'description',
        'state',
        'start_date',
        'end_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'area_id' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Obtener los pasantes asignados a este proyecto.
     */
    public function interns()
    {
        return $this->belongsToMany(Intern::class, 'assignments', 'project_id', 'intern_id')
            ->withPivot('role', 'assigned_date')
            ->withTimestamps();
    }

    /**
     * Calculate the progress percentage based on completed activities.
     *
     * @return int
     */
    public function getProgressPercentageAttribute(): int
    {
        // Use preloaded counts if available
        if (isset($this->attributes['activities_count']) && isset($this->attributes['completed_activities_count'])) {
            $totalActivities = $this->attributes['activities_count'];
            $completedActivities = $this->attributes['completed_activities_count'];
        } else {
            // Otherwise load activities and count
            $activities = $this->activities;

            if ($activities->isEmpty()) {
                return 0;
            }

            $totalActivities = $activities->count();
            $completedActivities = $activities->where('state', 'finalizado')->count();
        }

        if ($totalActivities === 0) {
            return 0;
        }

        return (int) (($completedActivities / $totalActivities) * 100);
    }
}
