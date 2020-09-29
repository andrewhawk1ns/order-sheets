<?php

namespace App\Exceptions;

use Exception;

class SheetsNotFoundException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json([
            'errors' => [
                'code' => 404,
                'title' => 'No Sheets Found',
                'detail' => 'No sheets to process.',
            ],
        ], 404);
    }
}
