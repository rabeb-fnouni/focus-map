<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'progress',
        'completed',
        'start_date',
        'end_date',
        'visibility',
        'location_name',
        'latitude',
        'longitude',
        'completed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'completed_at' => 'datetime',
        'completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function steps()
    {
        return $this->hasMany(Step::class)->orderBy('order');
    }

    // Optional: Keep if you need multiple locations; otherwise, remove
    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }

    public function sharedUsers()
    {
        return $this->belongsToMany(User::class, 'goal_sharing')
            ->withPivot('permission_level')
            ->withTimestamps();
    }

    public function mediaFiles()
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }

    public function achievements()
    {
        return $this->hasMany(\App\Models\Achievement::class);
    }

    public function calculateProgress()
    {
        $steps = $this->steps;
        if ($steps->isEmpty()) {
            return 0;
        }
        
        $completedSteps = $steps->where('completed', true)->count();
        return round(($completedSteps / $steps->count()) * 100);
    }
}