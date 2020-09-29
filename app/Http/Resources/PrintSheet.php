<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrintSheet extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'type' => 'print-sheets',
                'print_sheet_id' => $this->id,
                'attributes' => [
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at,
                    'type' => $this->type,
                    'sheet_url' => $this->sheet_url,
                ],
            ],
            'links' => [
                'self' => url('/print-sheets/' . $this->id),
            ],
        ];
    }
}
