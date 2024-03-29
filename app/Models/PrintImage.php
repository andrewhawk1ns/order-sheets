<?php

namespace App\Models;

use App\Models\Base;
use Illuminate\Support\Facades\Storage;

class PrintImage extends Base
{

    protected $width;
    protected $height;
    private $filePath;
    private $directory = '/app/tmp';
    private $url;

    public function __construct($url = '', $filePath = '')
    {
        $this->url = $url;

        $this->filePath = storage_path($filePath);

        if (!!$filePath) {
            $this->calculateDimensions();
        }

    }

    /**
     * Get the dimensions for the image being processed.
     *
     * @return void
     */
    public function calculateDimensions(): void
    {
        list($width, $height) = getimagesize($this->filePath);

        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Get the image's aspect ratio.
     *
     * @return int
     */
    public function getAspectRatio(): int
    {
        return $this->width / $this->height;
    }

    /**
     * Download an external file and return the content.
     *
     * @return string
     */
    private function downloadExternalFile(): string
    {
        $context = stream_context_create(array('http' => array('header' => 'Connection: close\r\n')));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $contents = curl_exec($ch);
        curl_close($ch);

        return $contents;
    }

    /**
     * Save the external image to the local file system.
     *
     * @param string $filePath
     * @return string
     */
    public function saveExternal(string $filePath): string
    {

        $content = $this->downloadExternalFile();

        $result = Storage::put($filePath, $content);

        $this->filePath = storage_path($filePath);

        return $filePath;
    }

}
