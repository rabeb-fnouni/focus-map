<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**

    /**
     * @return HasMany<Goal>
     */
    public function goals()
    {
        return $this->hasMany(Goal::class)->orderByDesc('created_at');
    }

    /**
 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
 */
    public function sharedGoals()
    {
        return $this->belongsToMany(Goal::class, 'goal_sharing')
            ->withPivot('permission_level')
            ->withTimestamps();
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot('achieved_at')
            ->withTimestamps();
    }

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }

    public function getAllGoals()
    {
        return $this->goals->merge($this->sharedGoals);
    }
    

}