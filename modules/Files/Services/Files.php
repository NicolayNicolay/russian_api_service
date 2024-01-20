<?php

namespace Modules\Files\Services;

use Modules\Files\Models\TmpFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Files extends File
{
    /**
     * @var UploadedFile
     */
    private $source_file = false;

    private $parent_dir = false;

    public string $hash = '';

    public string $sha1 = '';

    public function __construct($path, $dir = '')
    {
        $this->parent_dir = $dir;
        $this->source_file = $path;
        parent::__construct($path, true);
    }

    /**
     * Метод возвращает md5 хэш файла
     *
     * @return string
     */
    public function getHash()
    {
        if (! empty($this->hash)) {
            return $this->hash;
        }

        $this->hash = md5_file($this->getPathname());

        return $this->hash;
    }

    /**
     * Метод возвращает sha1 хэш файла
     *
     * @return string
     */
    public function getSha1()
    {
        if (! empty($this->sha1)) {
            return $this->sha1;
        }

        $this->sha1 = sha1_file($this->getPathname());

        return $this->sha1;
    }

    /**
     * Метод собирает путь к файлу
     *
     * @return string
     */
    public function getStoragePath()
    {
        $this->getHash();

        $path = '';

        $path .= mb_substr($this->hash, 0, 2);
        $path .= '/' . mb_substr($this->hash, 2, 2);
        $path .= '/' . mb_substr($this->hash, 4, 2);

        return $path;
    }

    /**
     * Метод получает расширение оригинального файла
     *
     * @return string
     */
    public function getFileExtension()
    {
        if (is_object($this->source_file)) {
            return $this->source_file->getClientOriginalExtension();
        }

        return $this->getExtension();
    }

    /**
     * Метод получает название оригинального файла
     *
     * @return string
     */
    public function getOriginalName()
    {
        if (is_object($this->source_file)) {
            return $this->source_file->getClientOriginalName();
        }

        return $this->getFilename();
    }

    /**
     * Получает путь к файлу для сохранения
     *
     * @return string
     */
    public function getSavePath()
    {
        $file_path_base = ! empty($this->parent_dir) ? $this->parent_dir . '/' : '';

        $file_path = $this->getStoragePath() . '/' . $this->getHash() . '.' . $this->getFileExtension();

        // Проверяем существование файла
        if (Storage::exists($file_path_base . $file_path)) {
            for ($i = 0; $i < 100; $i++) {
                $file_path = $this->getStoragePath() . '/' . $this->getHash() . '_' . $i . '.' . $this->getFileExtension();
                if (! Storage::exists($file_path_base . $file_path)) {
                    break;
                }
            }
        }

        return $file_path;
    }

    /**
     * Метод сохраняет файл в хранилище и регистрирует его в БД
     *
     * @return TmpFile|Model
     */
    public function saveFile(): TmpFile | Model
    {
        $saved_path = Storage::putFileAs($this->parent_dir, $this, $this->getSavePath());
        $storage_path = str_replace('public', 'storage', $saved_path);

        return (new TmpFile())->create(
            [
                'path'         => $saved_path,
                'storage_path' => $storage_path,
                'name'         => str_replace('.xlsx', '', $this->getOriginalName()),
            ]
        );
    }
}
