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

    /**
     * Gets the height of the PDF rows.
     *
     * @return int
     */
    public function getRowHeight(): int
    {
        return $this->height / $this->rows;
    }

    /**
     * Gets the width of the PDF rows.
     *
     * @return int
     */
    public function getRowWidth(): int
    {
        return $this->width / $this->columns;
    }

    /**
     * Gets the PDF's URL.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return asset('storage/pdf/' . $this->name);
    }

    /**
     * Saves the PDF to a file.
     *
     * @return string
     */
    public function save(): string
    {

        $pdfPath = $this->directoryPath . "/" . $this->name;

        $this->mpdf->Output($pdfPath, 'F');

        return $pdfPath;
    }

    /**
     * Adds a new page to the PDF for writing.
     *
     * @param int $rows
     * @param int $columns
     * @return void
     */
    public function addPage(int $rows = 15, int $columns = 10)
    {
        $this->mpdf->AddPage('P');
    }

    /**
     * Adds an image to the PDF.
     *
     * @param  string $path
     * @param  int $x
     * @param  int $y
     * @param  int $width
     * @param  int $height
     * @param  string $ext
     * @return string
     */
    public function addImage(string $path = '', int $x = 0, int $y = 0, int $width = 0, int $height = 0, string $ext = 'jpg'): string
    {
        $rowHeight = $this->getRowHeight();
        $rowWidth = $this->getRowWidth();

        $this->mpdf->Image($path, $x * $rowWidth, $y * $rowHeight, $width * $rowWidth, $height * $rowHeight, $ext, '', true, true);

        return $path;
    }

}
