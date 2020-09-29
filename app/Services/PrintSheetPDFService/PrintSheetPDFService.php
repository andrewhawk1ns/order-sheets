<?php

namespace App\Services\PrintSheetPDFService;

use App\Models\PrintImage;
use App\Models\PrintPDF;
use App\Models\Sheet;
use App\PrintSheet;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use PDF;

class PrintSheetPDFService
{
    private $pdf;
    private $mpdf;

    public function __construct()
    {
        $this->placeholderPath = '/app/public/placeholders/placeholder_1.jpg';
        $this->imageDirectory = '/app/tmp';

        $this->maybeCreateDirectories();
        $this->maybeCreatePlaceholder();
    }

    private function maybeCreateDirectories(): void
    {
        foreach ([$this->imageDirectory, '/app/public/pdf'] as $directory) {

            $directoryPath = storage_path($directory);
            if (!File::isDirectory($directoryPath)) {
                File::makeDirectory($directoryPath, 0777);
            }
        }
    }

    private function maybeCreatePlaceholder(): void
    {
        if (!File::exists(storage_path($this->placeholderPath))) {

            $image = new PrintImage('https://lorempixel.com/640/480/?77378');

            $image->saveExternal($this->placeholderPath);
        }

    }

    private function createPDF(int $sheetKey, \App\Models\Sheet $sheet): PrintPDF
    {
        $pdfView = PDF::loadHtml('', ['format' => 'Letter', 'orientation' => 'P']);
        $pdfName = $sheetKey . Carbon::now()->format('_Y_n_j_h_i_s') . '.pdf';

        $pdf = new PrintPDF($pdfView, $pdfName, $sheet->rows, $sheet->columns);

        return $pdf;
    }

    private function clearImages()
    {
        $file = new Filesystem;
        $file->cleanDirectory(storage_path($this->imageDirectory));
    }

    private function cropImageToSheetUnit(int $key = 1, PrintImage $image, $sheetItem): string
    {

        $aspectRatio = $image->getAspectRatio();

        $unitAspectRatio = $sheetItem->getAspectRatio();

        $multiplier = $aspectRatio > $unitAspectRatio ? $unitAspectRatio / $aspectRatio : $aspectRatio / $unitAspectRatio;

        $cropHeight = round($sheetItem->width > $sheetItem->height ? $multiplier * $image->height : $image->height);

        $cropWidth = round($sheetItem->height > $sheetItem->width ? $multiplier * $image->width : $image->width);

        $croppedImageName = Carbon::now()->format('Y_n_j_h_i_s');

        $croppedImagePath = storage_path($this->imageDirectory . "/$key$croppedImageName.jpg");

        try {

            $croppedImage = Image::make(storage_path($this->placeholderPath))->crop($cropWidth, $cropHeight)->save($croppedImagePath);

        } catch (\Exception $e) {
            return false;
        }

        return $croppedImagePath;
    }

    public function writeSheet(string $type = 'test', \App\Models\Sheet $sheet): PrintSheet
    {

        $sheetItems = [];

        $sheetItemsHeight = 0;

        $sheetItemsWidth = 0;

        $printSheet = PrintSheet::create(['type' => $type, 'sheet_url' => asset($sheet->pdf->getUrl())]);

        $now = Carbon::today()->toDateTimeString();

        foreach ($sheet->store as $sheetItemKey => $sheetItem) {

            $imageUrl = $sheetItem->item->product->design_url;

            $image = new PrintImage('', $this->placeholderPath);

            $croppedImagePath = $this->cropImageToSheetUnit($sheetItemKey, $image, $sheetItem);

            if (!$croppedImagePath) {
                continue;
            }

            $sheetItemsHeight += $sheetItem->height;

            $sheetItemsWidth += $sheetItem->width;

            $sheet->pdf->addImage($croppedImagePath, $sheetItem->x, $sheetItem->y, $sheetItem->width, $sheetItem->height);

            $sheetItems[$sheetItemKey] = ['status' => 'complete', 'image_url' => $imageUrl, 'size' => $sheetItem->item->product->size, 'x_pos' => $sheetItem->x,
                'y_pos' => $sheetItem->y, 'width' => $sheetItem->width, 'height' => $sheetItem->height, 'identifier' => 'test', 'order_item_id' => $sheetItem->item->id, 'print_sheet_id' => $printSheet->id, 'created_at' => $now, 'updated_at' => $now];
        }

        $sheet->pdf->save();

        // var_dump($sheetItems);

        $printSheet->items()->insert($sheetItems);

        return $printSheet;

    }

    public function processSheets(string $type, array $sheets): Collection
    {

        $printedSheets = [];

        foreach ($sheets as $sheetKey => $sheet) {

            $sheet->pdf = $this->createPDF($sheetKey, $sheet);
            $printResult = $this->writeSheet($type, $sheet);

            $printedSheets[] = $printResult;

            $this->clearImages();
        }

        return collect($printedSheets);
    }

}
