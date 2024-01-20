<?php

declare(strict_types=1);

namespace Modules\Files\Controllers;

use App\Http\Controllers\Controller;
use Modules\Files\Models\File;
use Modules\Files\Models\TmpFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Files\Services\Files;

class FileController extends Controller
{
    /**
     * Сохраняет файл и регистрирует в БД
     *
     * @param Request $request
     * @return JsonResponse|Model|TmpFile
     */
    public function uploadFile(Request $request): Model | JsonResponse | TmpFile
    {
        $photo = $request->file('fileOrder');
        $disallow_extensions = ['php'];
        if ($photo && $photo->isFile() && ! in_array(strtolower($photo->getClientOriginalExtension()), $disallow_extensions)) {
            $file = new Files($photo, 'files');
            return response()->json(
                $file->saveFile(),
                200
            );
        }
        return response()->json(
            [
                'error'   => true,
                'message' => 'Ошибка при загрузке файла. ' . (! empty($photo) ? $photo->getErrorMessage() : ''),
            ],
            500
        );
    }
}
