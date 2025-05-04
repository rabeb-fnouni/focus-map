<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',          
        'description',
        'badge_icon',
        'achievement_type',
        'threshold',
        'goal_id'      
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot('achieved_at')
            ->withTimestamps();
    }
    public function goal()
{
    return $this->belongsTo(\App\Models\Goal::class);
}
}
