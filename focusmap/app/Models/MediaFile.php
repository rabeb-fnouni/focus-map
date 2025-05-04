<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'mediable_id',
        'mediable_type',
        'path',
        'file_name',
        'mime_type',
        'size',
    ];

    public function mediable()
    {
        return $this->morphTo();
    }
}