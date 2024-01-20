<?php

declare(strict_types=1);

namespace Modules\Files\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\Pure;

/**
 * App\Models\PhotoTmp
 *
 * @mixin Builder
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TmpFile extends Model
{
    protected $table = 'tmp_files';

    protected $fillable = ['path', 'storage_path', 'name', 'status', 'csv_path'];

    /**
     * Путь к файлу в файловой системе
     *
     * @return string
     */
    public function getStoragePathAttribute(): string
    {
        return Storage::path($this->path);
    }

    /**
     * Доп. атрибут для хранения состояния чекбокса во фронте
     *
     * @return bool
     */
    public function getCheckedAttribute(): bool
    {
        return false;
    }

    /**
     * Доп. атрибут для хранения состояния файла к удалению
     *
     * @return bool
     */
    public function getToDeleteAttribute(): bool
    {
        return false;
    }
    /**
     * Метод получает расширение файла
     *
     * @return string
     */
    public function getExtension(): string
    {
        return mb_strtolower((string) pathinfo($this->path, PATHINFO_EXTENSION));
    }

    /**
     * Имя файла без расширения
     *
     * @return string
     */
    public function getFileName(): string
    {
        return pathinfo($this->path, PATHINFO_FILENAME);
    }

    /**
     * Имя файла без расширения
     *
     * @return string
     */
    #[Pure]
    public function getPathWithoutExtension(): string
    {
        $path = pathinfo($this->path, PATHINFO_DIRNAME);
        return $path . '/' . $this->getFileName();
    }

    public function scopeOldObjects(Builder $query): Builder
    {
        return $query->where('status', '=', 1)->orWhereRaw('created_at < DATE_SUB(DATE(NOW()), INTERVAL 7 DAY)');
    }
}
