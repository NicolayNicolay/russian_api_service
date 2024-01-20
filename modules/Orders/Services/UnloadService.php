<?php

namespace Modules\Orders\Services;

use Carbon\Carbon;
use File;
use Illuminate\Support\Facades\Storage;
use Modules\Orders\Resources\UnloadResource;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UnloadService
{
    public $data;

    public string $path;

    public string $filename;

    const DEFAULT_END = 'O';
    const DEFAULT_UNLOAD_PATH = 'unload/';

    public function __construct($data)
    {
        $this->data = $data;
        $this->filename = 'orders_' . date('dmY_His', strtotime(Carbon::now())) . '.xlsx';
    }

    /**
     * @throws Exception
     */
    public function generate()
    {
        try {
            $rowValue = [];
            $rowColors = [];
            foreach ($this->data as $key => $item) {
                $rowValue[] = (new UnloadResource($item))->toArray();
                $rowColors[$key] = $item->table_color;
            }

            $spreadsheet = new Spreadsheet();
            $worksheet = $spreadsheet->getActiveSheet();
            $exportDataTitleMerge = [];
            $header = config('orders.header');
            $first_merge = config('orders.header_rows_merge');
            $result = array_merge($header, $rowValue);
            foreach ($header[0] as $columnNum => $columnValue) {
                if (in_array($columnNum, $first_merge)) {
                    $ColumnIndex = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($columnNum + 1, 1)->getCoordinate();
                    $ColumnIndex2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($columnNum + 1, 2)->getCoordinate();
                    $exportDataTitleMerge[] = $ColumnIndex . ':' . $ColumnIndex2;
                }
            }
            $exportDataTitleMerge[] = 'L1:N1';
            $exportDataTitleMerge[] = 'O1:R1';

            $worksheet->fromArray($result);
            foreach ($exportDataTitleMerge as $exportDataTitleMergeItem) {
                $spreadsheet->getActiveSheet()->mergeCells($exportDataTitleMergeItem);
            }
            $styleArray = [
                'font'      => [
                    'bold' => true,
                ],
                'alignment' => [
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
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
            ];
            $styleArray2 = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ];
            // Ширина колонок
            foreach ($header[0] as $columnNum => $columnValue) {
                $cellIndex = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($columnNum + 1, 1)->getCoordinate();
                $spreadsheet->getActiveSheet()->getStyle($cellIndex)->applyFromArray($styleArray);
                $cellIndex2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($columnNum + 1, 2)->getCoordinate();
                $spreadsheet->getActiveSheet()->getStyle($cellIndex2)->applyFromArray($styleArray);
                $columnIndex = Coordinate::stringFromColumnIndex($columnNum + 1);
                if (! in_array($columnNum, $first_merge)) {
                    $spreadsheet->getActiveSheet()->getColumnDimension($columnIndex)->setWidth(20);
                    $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getAlignment()->setWrapText(true);
                    $spreadsheet->getActiveSheet()->getStyle($columnIndex . ':' . $columnIndex)->applyFromArray($styleArray2);
                } else {
                    $spreadsheet->getActiveSheet()->getColumnDimension($columnIndex)->setWidth(18);
                    $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getAlignment()->setWrapText(true);
                }
            }

            $spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(60);
            $spreadsheet->getActiveSheet()->getRowDimension(2)->setRowHeight(20);
            foreach ($rowValue as $key => $values) {
                foreach ($values as $index => $column) {
                    $styleArrayRow = [
                        'borders' => [
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
                        'fill'    => [
                            'fillType' => Fill::FILL_SOLID,
                            'color'    => [
                                'argb' => $rowColors[$key] ? str_replace('#', '', $rowColors[$key]) : 'ffffff',
                            ],
                        ],
                    ];
                    $cellIndex = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($index + 1, ($key + 3))->getCoordinate();
                    $spreadsheet->getActiveSheet()->getStyle($cellIndex)->applyFromArray($styleArrayRow);
                }
            }
            // Save to file.
            if (! File::exists(self::DEFAULT_UNLOAD_PATH)) {
                File::makeDirectory(self::DEFAULT_UNLOAD_PATH, 0777, true);
            }
            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            ob_start();
            $writer->save(self::DEFAULT_UNLOAD_PATH . $this->filename);
            $content = ob_get_contents();
            ob_end_clean();

            Storage::disk('public')->put(self::DEFAULT_UNLOAD_PATH . $this->filename, $content);

            return ['status' => true, 'path' => asset(self::DEFAULT_UNLOAD_PATH . $this->filename)];
        } catch (\Exception $exception) {
            return ['status' => false, 'errors' => $exception->getMessage()];
        }
    }
}
