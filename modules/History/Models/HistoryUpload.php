<?php

namespace Modules\History\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Modules\Files\Models\TmpFile;
use Modules\Seasons\Models\Seasons;
use Modules\Users\Models\User;

class HistoryUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_id',
        'season_id',
        'status',
        'processed',
        'count_string'
    ];
    protected $appends = [
        'full_path',
    ];
    protected $casts = [
        'created_at' => 'date:d.m.Y H:i:s',
        'updated_at' => 'date:d.m.Y H:i:s',
    ];
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function file(): HasOne
    {
        return $this->hasOne(TmpFile::class, 'id', 'file_id');
    }
    public function season(): HasOne
    {
        return $this->hasOne(Seasons::class, 'id', 'season_id');
    }

    /**
     * Полный путь к файлу
     *
     * @return string
     */
    public function getFullPathAttribute(): string
    {
        return '/storage/' . $this->file->path;
    }
}
