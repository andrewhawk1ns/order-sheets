<?php

namespace App\Models;

class PrintPDF extends Base
{

    private $pdf;
    private $mpdf;
    private $rows;
    private $columns;
    private $name;
    private $width = 215.8;
    private $height = 269.4;
    private $directoryPath;

    public function __construct($pdfView, string $name, int $rows = 15, int $columns = 10, string $directory = '/app/public/pdf')
    {

        $this->rows = $rows;
        $this->columns = $columns;
        $this->pdf = $pdfView;
        $this->mpdf = $this->pdf->getMpdf();
        $this->directoryPath = storage_path($directory);
        $this->name = $name;
    }

    public function getRowHeight(): int
    {
        return $this->height / $this->rows;
    }

    public function getRowWidth(): int
    {
        return $this->width / $this->columns;
    }

    public function getUrl(): string
    {
        return asset('storage/pdf/' . $this->name);
    }

    public function save(): string
    {

        $pdfPath = $this->directoryPath . "/" . $this->name;

        $this->mpdf->Output($pdfPath, 'F');

        return $pdfPath;
    }

    public function addPage(int $rows = 15, int $columns = 10)
    {
        $this->mpdf->AddPage('P');
    }

    public function addImage(string $path = '', int $x = 0, int $y = 0, int $width = 0, int $height = 0, string $ext = 'jpg'): string
    {
        $rowHeight = $this->getRowHeight();
        $rowWidth = $this->getRowWidth();

        $this->mpdf->Image($path, $x * $rowWidth, $y * $rowHeight, $width * $rowWidth, $height * $rowHeight, $ext, '', true, true);

        return $path;
    }

}
