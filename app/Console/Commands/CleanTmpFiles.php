<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\DropTmpFiles;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Modules\Files\Models\TmpFile;
use Modules\History\Models\HistoryUpload;

class CleanTmpFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:tmp-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получение устаревших временных файлов и добавление их в очередь на удаление';

    /**
     * Выполняем команду по переопределению районов
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->info('Выбираем устаревшие временные фото');

        // Получаем фото, которые являются временными уже больше 5 часов
        $date = Carbon::now()->addHours(-5);
        $tmp_photos = (new TmpFile())->where('created_at', '<', $date)->where('status', 0)->get();
        foreach ($tmp_photos as $photo) {
            $relation = (new HistoryUpload())->where('file_id', '=', $photo->id)->first();
            if ($photo->csv_path) {
                Storage::disk('public')->delete($photo->csv_path);
            }
            if ($relation === null) {
                Storage::disk('public')->delete($photo->path);
                $photo->delete();
            }
        }

        $this->info('Выполнено');
    }
}
