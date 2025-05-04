<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'title',
        'category',
        'description',
        'progress',
        'deadline',
        'completed',
        'order',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'deadline' => 'date',
    ];

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    public function mediaFiles()
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }
}