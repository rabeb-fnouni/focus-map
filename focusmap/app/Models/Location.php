<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'title',
        'description',
        'latitude',
        'longitude',
    ];

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }
}
