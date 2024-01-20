<?php

declare(strict_types=1);

namespace Modules\Admin\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportService
{
    public function generateExcel(array $data): string
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->getStyle([1, 1, count($data[0]), 1])
            ->applyFromArray(
                [
                    'font'      => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                    'borders'   => [
                        'top'    => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                        'left'   => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                        'right'  => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'fill'      => [
                        'fillType' => Fill::FILL_SOLID,
                        'rotation' => 90,
                        'color'    => [
                            'argb' => 'EAEAEA',
                        ],
                    ],
                ]
            );

        $worksheet->getStyle('A2' . ':' . 'V' . count($data))
            ->applyFromArray(
                [
                    'font'      => [
                        'bold' => false,
                    ],
                    'alignment' => [
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]
            );

        $worksheet->fromArray($data);

        foreach ($worksheet->getColumnIterator() as $column) {
            $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        // Save to file.
        if (! is_dir('storage/unloadOrders')) {
            mkdir('storage/unloadOrders');
        }

        $path = 'storage/unloadOrders/' . date('d.m.Y H:i:s') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->save($path);

        return $path;
    }
}
