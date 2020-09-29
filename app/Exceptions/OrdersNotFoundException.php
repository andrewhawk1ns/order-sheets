<?php

namespace App\Exceptions;

use Exception;

class OrdersNotFoundException extends Exception
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
                'title' => 'No Orders Found',
                'detail' => 'No orders available to process to sheets.',
            ],
        ], 404);
    }
}
